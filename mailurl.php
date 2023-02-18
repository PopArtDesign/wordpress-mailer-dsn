<?php

/**
 * Plugin Name: Mail URL
 * Plugin URI:  https://github.com/voronkovich
 * Description: Configure wp_mail() via MAIL_URL environment variable
 * Version:     0.0.1
 * License:     MIT
 * License URI: https://github.com/voronkovich/wordpress-mail-url/blob/main/LICENSE
 * Author:      Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * Author URI:  https://github.com/voronkovich
 * Text Domain: mailurl
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

add_action('phpmailer_init', 'mailurl_phpmailer_init');

function mailurl_phpmailer_init($phpmailer)
{
    $dsn = \mailurl_get_dsn();

    if ($dsn) {
        \mailurl_phpmailer_configure($phpmailer, $dsn);
    }
}

function mailurl_get_dsn()
{
    return defined('MAIL_URL') ? MAIL_URL : getenv('MAIL_URL');
}

function mailurl_phpmailer_configure($phpmailer, $dsn)
{
    $config = mailurl_parse_dsn($dsn);

    switch ($config['scheme']) {
        case 'mail':
            $phpmailer->isMail();
            break;
        case 'semdmail':
            $phpmailer->isSendmail();
            break;
        case 'qmail':
            $phpmailer->isQmail();
            break;
        case 'smtp':
            mailurl_phpmailer_configure_smtp($phpmailer, $config);
            break;
    }

    mailurl_phpmailer_configure_options($phpmailer, $config);

    dump($phpmailer);

    return $phpmailer;
}

function mailurl_parse_dsn($dsn)
{
    if (false === $config = parse_url($dsn)) {
        throw new \RuntimeException(
            \sprintf('Mailformed mail URL: %s.', $dsn)
        );
    }

    $config['scheme'] = $config['scheme'] ?? $config['path'];
    $allowedSchemes = ['mail', 'sendmail', 'qmail', 'smtp'];

    if (!$config['scheme']) {
        throw new \RuntimeException(
            \sprintf(
                'Mail URL scheme not set: "%s". Allowed values: "%s".',
                $dsn,
                implode('", "', $allowedSchemes),
            )
        );
    }

    if (!in_array($config['scheme'], $allowedSchemes)) {
        throw new \RuntimeException(
            \sprintf(
                'Invalid mail URL scheme: "%s". Allowed values: "%s".',
                $config['scheme'],
                implode('", "', $allowedSchemes),
            )
        );
    }

    return $config;
}

function mailurl_phpmailer_configure_smtp($phpmailer, $config)
{
    $phpmailer->isSMTP();

    $phpmailer->Host = $config['host'] ?? 'localhost';
    $phpmailer->Port = $config['port'] ?? 25;

    if (isset($config['user']) || isset($config['pass'])) {
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $config['user'] ?? '';
        $phpmailer->Password = $config['pass'] ?? '';
    } else {
        $phpmailer->SMTPAuth = false;
    }
}

function mailurl_phpmailer_configure_options($phpmailer, $config)
{
    if (!isset($config['query'])) {
        return;
    }

    $options = [];
    \parse_str($config['query'], $options);

    $allowedOptions = \get_object_vars($phpmailer);
    unset($allowedOptions['Mailer']);
    unset($allowedOptions['SMTPAuth']);
    unset($allowedOptions['Username']);
    unset($allowedOptions['Password']);
    unset($allowedOptions['Hostname']);
    unset($allowedOptions['Port']);
    unset($allowedOptions['ErrorInfo']);
    $allowedOptions = \array_keys($allowedOptions);

    foreach ($options as $key => $value) {
        if (!\in_array($key, $allowedOptions)) {
            throw new \RuntimeException(
                \sprintf(
                    'Unknown mail URL option: "%s". Allowed values: "%s"',
                    $key,
                    implode('", "', $allowedOptions),
                )
            );
        }

        if ('true' === $value) {
            $phpmailer->$key = true;
            continue;
        }

        if ('false' === $value) {
            $phpmailer->$key = false;
            continue;
        }

        if (\is_numeric($value)) {
            $phpmailer->$key = (integer) $value;
            continue;
        }

        $phpmailer->$key = $value;
    }
}
