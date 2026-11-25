<?php // AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 24.11 ?>
<?php get_header(); ?>

<section class="ikke-fundet">
    <h1>Siden findes ikke</h1>
    <p>Vi kunne ikke finde den side, du leder efter. Måske er linket forkert, eller også er cyklen solgt.</p>
    <p>
        <a class="knap knap--primaer" href="<?php echo esc_url(home_url('/')); ?>">Til forsiden</a>
        <a class="knap knap--kant" href="<?php echo esc_url(get_post_type_archive_link('kk_cykel')); ?>">Se brugte cykler</a>
    </p>
</section>

<?php get_footer(); ?>
