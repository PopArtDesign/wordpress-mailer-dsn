<?php

/**
 * Plugin Name: Mailer DSN
 * Plugin URI:  https://github.com/voronkovich/wordpress-mailer-dsn
 * Description: Configure wp_mail() via MAILER_DSN environment variable
 * Version:     0.0.2
 * License:     MIT
 * License URI: https://github.com/voronkovich/wordpress-mailer-dsn/blob/main/LICENSE
 * Author:      Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * Author URI:  https://github.com/voronkovich
 */

defined('ABSPATH') || exit;

add_action('phpmailer_init', function ($phpmailer) {
    require_once __DIR__ . '/functions.php';

    \mailerdsn_phpmailer_init($phpmailer);
});
