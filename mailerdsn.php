<?php

/**
 * Plugin Name: Mailer DSN
 * Plugin URI:  https://github.com/PopArtDesign/wordpress-mailer-dsn
 * Description: Configure wp_mail() via MAILER_DSN environment variable
 * Version:     0.0.7
 * License:     MIT
 * License URI: https://github.com/PopArtDesign/wordpress-mailer-dsn/blob/main/LICENSE
 * Author:      Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * Author URI:  https://github.com/voronkovich
 */

defined('ABSPATH') || exit;

use PHPMailer\PHPMailer\DSNConfigurator;

add_action('phpmailer_init', function ($phpmailer) {
    static $configurator = null;

    if (!$dsn = defined('MAILER_DSN') ? MAILER_DSN : getenv('MAILER_DSN')) {
        return;
    }

    $configurator = $configurator ?? new DSNConfigurator();

    $configurator->configure($phpmailer, $dsn);
});
