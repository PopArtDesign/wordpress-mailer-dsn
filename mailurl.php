<?php

/**
 * Plugin Name: Mail URL
 * Plugin URI:  https://github.com/voronkovich/wordpress-mail-url
 * Description: Configure wp_mail() via MAIL_URL environment variable
 * Version:     0.0.1
 * License:     MIT
 * License URI: https://github.com/voronkovich/wordpress-mail-url/blob/main/LICENSE
 * Author:      Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * Author URI:  https://github.com/voronkovich
 */

defined('ABSPATH') || exit;

require __DIR__ . '/functions.php';

add_action('phpmailer_init', 'mailurl_phpmailer_init');
