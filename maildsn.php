<?php

/**
 * Plugin Name: Mail DSN
 * Plugin URI:  https://github.com/voronkovich
 * Description: Configure wp_mail() via MAILER_DSN environment variable
 * Version:     0.0.1
 * License:     MIT
 * License URI: https://github.com/voronkovich/wp-mail-dsn/blob/main/LICENSE
 * Author:      Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * Author URI:  https://github.com/voronkovich
 * Text Domain: maildsn
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

add_action('phpmailer_init', 'maildsn_phpmailer_init');

function maildsn_phpmailer_init($phpmailer)
{
    $dsn = \maildsn_get_dsn();

    if ($dsn) {
        \maildsn_phpmailer_configure($phpmailer, $dsn);
    }
}

function maildsn_get_dsn()
{
    return defined('MAILER_DSN') ? MAILER_DSN : getenv('MAILER_DSN');
}

function maildsn_phpmailer_configure($phpmailer, $dsn)
{
    $config = maildsn_parse_dsn($dsn);

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
            maildsn_phpmailer_configure_smtp($phpmailer, $config);
            break;
    }

    maildsn_phpmailer_configure_options($phpmailer, $config);

    dump($phpmailer);

    return $phpmailer;
}

function maildsn_parse_dsn($dsn)
{
    if (false === $config = parse_url($dsn)) {
        throw new \RuntimeException(
            \sprintf('Mailformed mailer DSN: %s.', $dsn)
        );
    }

    $config['scheme'] = $config['scheme'] ?? $config['path'];
    $allowedSchemes = ['mail', 'sendmail', 'qmail', 'smtp'];

    if (!$config['scheme']) {
        throw new \RuntimeException(
            \sprintf(
                'Mailer DSN scheme not set: "%s". Allowed values: "%s".',
                $dsn,
                implode('", "', $allowedSchemes),
            )
        );
    }

    if (!in_array($config['scheme'], $allowedSchemes)) {
        throw new \RuntimeException(
            \sprintf(
                'Invalid mailer DSN scheme: "%s". Allowed values: "%s".',
                $config['scheme'],
                implode('", "', $allowedSchemes),
            )
        );
    }

    return $config;
}

function maildsn_phpmailer_configure_smtp($phpmailer, $config)
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

function maildsn_phpmailer_configure_options($phpmailer, $config)
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
                    'Unknown mailer DSN option: "%s". Allowed values: "%s"',
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
