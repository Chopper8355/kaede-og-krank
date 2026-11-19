<?php
/**
 * Plugin Name: Secure SVG Upload
 * Description: Tillader SVG-upload i mediebiblioteket for administratorer og saniterer hver fil (allowlist af elementer/attributter, ingen script, on*-handlers, eksterne referencer, DTD/ENTITY; style-elementer kun med lokal CSS) foer den gemmes.
 * Version: 1.1.0
 * Author: Jonas Wendt Knudsen
 * Text Domain: secure-svg-upload
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit; // Ingen direkte adgang

// Tillader SVG upload -- kun for administratorer. En SVG er XML og kan baere
// <script>, on*-handlers og javascript:-URLer, der koerer paa sitets domaene
// med admin-sessionen. Derfor ogsaa den obligatoriske sanitering nedenfor.
function secure_svg_upload_allow_mime($mimes) {
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'secure_svg_upload_allow_mime');

function secure_svg_upload_fix_mime_type($data, $file, $filename, $mimes) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'svg' && current_user_can('manage_options')) {
        $data['type'] = 'image/svg+xml';
        $data['ext']  = 'svg';
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'secure_svg_upload_fix_mime_type', 10, 4);

// Saniterer SVG-filer foer de lander i uploads-mappen. Filen omskrives paa
// disken, saa den gemte fil altid er den rensede version.
function secure_svg_upload_prefilter($file) {
    if (!empty($file['error']) || empty($file['tmp_name'])) {
        return $file;
    }

    $ext  = isset($file['name']) ? strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) : '';
    $type = isset($file['type']) ? strtolower($file['type']) : '';
    if ($ext !== 'svg' && $type !== 'image/svg+xml') {
        return $file;
    }

    if (!current_user_can('manage_options')) {
        $file['error'] = __('Kun administratorer maa uploade SVG-filer.', 'secure-svg-upload');
        return $file;
    }

    $raw = file_get_contents($file['tmp_name']); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- lokal tmp-fil, ikke en URL
    if ($raw === false || trim($raw) === '') {
        $file['error'] = __('SVG-filen kunne ikke laeses.', 'secure-svg-upload');
        return $file;
    }

    $clean = secure_svg_upload_sanitize_markup($raw);
    if ($clean === false) {
        $file['error'] = __('SVG-filen kunne ikke valideres og blev afvist.', 'secure-svg-upload');
        return $file;
    }

    if (file_put_contents($file['tmp_name'], $clean) === false) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- omskriver upload-tmp-filen
        $file['error'] = __('SVG-filen kunne ikke gemmes efter sanitering.', 'secure-svg-upload');
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'secure_svg_upload_prefilter');
add_filter('wp_handle_sideload_prefilter', 'secure_svg_upload_prefilter');

// Parser SVG'en som XML og genopbygger den ud fra en allowlist af elementer
// og attributter. Returnerer den rensede markup, eller false hvis filen ikke
// kan parses / ikke er en aegte SVG.
function secure_svg_upload_sanitize_markup($svg) {
    if (!class_exists('DOMDocument')) {
        return false;
    }

    // Afvis DTD og ENTITY helt (XXE og "billion laughs") -- ingen legitim
    // logo-SVG har brug for dem.
    if (preg_match('/<!\s*(DOCTYPE|ENTITY)/i', $svg)) {
        return false;
    }

    // Bevidst udeladt: script, foreignObject, animate/set (kan saette
    // href til javascript:), feImage (kan hente eksternt indhold).
    // style er tilladt, men indholdet tjekkes i secure_svg_upload_clean_style().
    $allowed_tags = array(
        'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'metadata', 'style',
        'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'textpath',
        'lineargradient', 'radialgradient', 'stop', 'pattern',
        'clippath', 'mask', 'marker', 'switch',
        'filter', 'fegaussianblur', 'feoffset', 'feblend', 'fecolormatrix',
        'femerge', 'femergenode', 'feflood', 'fecomposite', 'fedropshadow',
        'fefunca', 'fefuncb', 'fefuncg', 'fefuncr', 'fecomponenttransfer',
    );

    $prev_errors = libxml_use_internal_errors(true);
    // PHP < 8 loader eksterne entiteter som standard
    if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
        $prev_loader = libxml_disable_entity_loader(true);
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $loaded = $dom->loadXML($svg, LIBXML_NONET);

    if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
        libxml_disable_entity_loader($prev_loader);
    }
    libxml_clear_errors();
    libxml_use_internal_errors($prev_errors);

    if (!$loaded || !$dom->documentElement) {
        return false;
    }
    if (strtolower($dom->documentElement->localName) !== 'svg') {
        return false;
    }

    // Fjern processing instructions paa dokumentniveau (fx xml-stylesheet)
    foreach (iterator_to_array($dom->childNodes) as $node) {
        if ($node->nodeType === XML_PI_NODE) {
            $dom->removeChild($node);
        }
    }

    secure_svg_upload_scrub_node($dom->documentElement, $allowed_tags);

    $out = $dom->saveXML();
    return ($out === false) ? false : $out;
}

// Rekursiv oprydning af et enkelt element og dets boern.
function secure_svg_upload_scrub_node($node, $allowed_tags) {
    foreach (iterator_to_array($node->childNodes) as $child) {
        if ($child->nodeType === XML_PI_NODE || $child->nodeType === XML_COMMENT_NODE) {
            $node->removeChild($child);
            continue;
        }
        if ($child->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        // Strip namespace-praefiks foer sammenligning (fx svg:script)
        $name = strtolower($child->localName);
        if (!in_array($name, $allowed_tags, true)) {
            $node->removeChild($child);
            continue;
        }
        if ($name === 'style' && !secure_svg_upload_clean_style($child)) {
            $node->removeChild($child);
            continue;
        }
        secure_svg_upload_scrub_node($child, $allowed_tags);
    }

    foreach (iterator_to_array($node->attributes) as $attr) {
        $attr_name = strtolower($attr->localName);
        $value     = $attr->value;

        // on*-handlers -- den direkte XSS-vektor
        if (strpos($attr_name, 'on') === 0) {
            $node->removeAttributeNode($attr);
            continue;
        }

        // Kun lokale referencer i href/xlink:href, saa <use> ikke kan hente
        // fjern-indhold ind
        if ($attr_name === 'href' || $attr_name === 'src') {
            if (strpos(ltrim($value), '#') !== 0) {
                $node->removeAttributeNode($attr);
            }
            continue;
        }

        // javascript:, data:text/html osv. i fx style eller fill="url(...)"
        $flat = strtolower(preg_replace('/\s+|&#\w+;/', '', $value));
        if (strpos($flat, 'javascript:') !== false
            || strpos($flat, 'vbscript:') !== false
            || strpos($flat, 'data:text/html') !== false
            || strpos($flat, '@import') !== false
            || strpos($flat, 'expression(') !== false) {
            $node->removeAttributeNode($attr);
        }
    }
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 19.11
// Renser indholdet af et <style>-element, saa fx animationer kan blive i
// SVG'en. Returnerer false, hvis elementet skal fjernes helt.
// Reglen er: CSS'en maa kun style SVG'en selv -- den maa ikke hente noget
// udefra (url(), @import, @font-face, image-set() ...) og ikke indeholde
// tegn, der kan bruges til at snyde tjekket (CSS-escapes) eller bryde ud af
// <style>, naar SVG'en saettes inline i HTML.
function secure_svg_upload_clean_style($style) {
    $css = '';
    foreach (iterator_to_array($style->childNodes) as $child) {
        if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
            $css .= $child->nodeValue;
        } elseif ($child->nodeType !== XML_COMMENT_NODE) {
            return false; // Elementer inde i <style> giver ingen mening
        }
    }

    // CSS-kommentarer fjernes, saa de ikke kan dele et noegleord op
    $css = preg_replace('#/\*.*?\*/#s', '', $css);
    if ($css === null || strpos($css, '/*') !== false) {
        return false;
    }

    // Backslash = CSS-escape (fx \75 rl( for url( ), < = </style> ved inline
    if (strpos($css, '\\') !== false || strpos($css, '<') !== false) {
        return false;
    }

    $flat = strtolower(preg_replace('/\s+/', '', $css));
    $blocked = array(
        '@import', '@font-face', '@namespace', 'image-set(', 'image(',
        'cross-fade(', 'element(', 'expression(', 'javascript:', 'vbscript:',
        'behavior:', '-moz-binding',
    );
    foreach ($blocked as $word) {
        if (strpos($flat, $word) !== false) {
            return false;
        }
    }

    // Kun lokale referencer: url(#id), url('#id') eller url("#id")
    $without_local = preg_replace('/url\(([\'"]?)#[\w.:-]+\1\)/', '', $flat);
    if ($without_local === null || strpos($without_local, 'url(') !== false) {
        return false;
    }

    // Erstat indholdet med den rensede CSS som ren tekst
    while ($style->firstChild) {
        $style->removeChild($style->firstChild);
    }
    $style->appendChild($style->ownerDocument->createTextNode(trim($css)));
    return true;
}
