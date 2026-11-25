</main>

<?php
// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 23.11
$aabningstider = get_option('kk_aabningstider', []);
?>
<footer class="site-footer">
    <div class="site-footer__indhold">

        <div class="site-footer__kolonne logo_kolonne">
            <img class="logo" src="<?php echo esc_url(get_theme_file_uri('assets/logo/logo-negativ.svg')); ?>"
                 alt="Kæde &amp; Krank" width="452" height="104" loading="lazy">
            <p class="site-footer__slogan">Vi holder Aarhus rullende</p>
        </div>

        <div class="site-footer__kolonne kontakt_kolonne">
            <h2 class="site-footer__overskrift">Kontakt</h2>
            <address class="site-footer__kontakt">
                <p>Kæde &amp; Krank<br>Krankvej 12<br>8000 Aarhus C</p>
                <p><a href="tel:+4586123456">86 12 34 56</a></p>
                <p><a href="mailto:hej@kaedeogkrank.dk">hej@kaedeogkrank.dk</a></p>
            </address>
        </div>

        <div class="site-footer__kolonne aabningstider_kolonne">
            <h2 class="site-footer__overskrift">Åbningstider</h2>
            <dl class="aabningstider">
                <?php foreach ($aabningstider as $dag) : ?>
                    <div class="aabningstider__raekke">
                        <dt><?php echo esc_html($dag['dag']); ?></dt>
                        <dd>
                            <?php echo $dag['aaben']
                                ? esc_html($dag['aaben'] . '–' . $dag['lukker'])
                                : 'Lukket'; ?>
                        </dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>

    </div>

    <p class="site-footer__bund">
        © <?php echo esc_html(wp_date('Y')); ?> Kæde &amp; Krank · CVR 12 34 56 78
    </p>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>