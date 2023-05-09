<?php

/**
 * Plugin Name: Mailer DSN
 * Plugin URI:  https://github.com/PopArtDesign/wordpress-mailer-dsn
 * Description: Configure wp_mail() via environment variables (e.g. MAILER_DSN)
 * Version:     1.0.0
 * License:     MIT
 * License URI: https://github.com/PopArtDesign/wordpress-mailer-dsn/blob/main/LICENSE
 * Author:      PopArtDesign <info@popartdesign.ru>
 * Author URI:  https://popartdesign.ru
 */

defined('ABSPATH') || exit;

use PopArtDesign\WordPressMailerDSN\PHPMailerConfigurator;

add_action('phpmailer_init', function ($mailer) {
    static $configurator = null;

    $configurator = $configurator ?? new PHPMailerConfigurator();

    $configurator->configure($mailer);
});
