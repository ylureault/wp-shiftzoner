<?php
/**
 * The footer template file
 *
 * @package ShiftZoneR
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <p>
                &copy; <?php echo date('Y'); ?>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
                - <?php esc_html_e('Tous droits réservés', 'shiftzoner'); ?>
            </p>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
