=== Secure SVG Upload ===
Contributors: jonaswendtknudsen
Tags: svg, upload, security, sanitize, media
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allows administrators to upload SVG files and sanitizes every file before it is stored.

== Description ==

* SVG upload is only enabled for users with the `manage_options` capability.
* Each SVG is parsed as XML and rebuilt from an allowlist of elements and attributes.
* Removes scripts, foreignObject, style, animate/set, on* event handlers, external href/src references and javascript:/data:text/html values.
* Rejects files containing DOCTYPE or ENTITY declarations (XXE / billion laughs).
* The sanitized version overwrites the uploaded file, so only clean markup reaches the uploads folder.

== Changelog ==

= 1.0.0 =
* Extracted from CarsWiki Core into a standalone plugin.
