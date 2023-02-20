<?php

defined('ABSPATH') || exit;

function mailerdsn_phpmailer_init($phpmailer)
{
    $url = \mailerdsn_get_dsn();

    if ($url) {
        \mailerdsn_phpmailer_configure($phpmailer, $url);
    }
}

function mailerdsn_get_dsn()
{
    return \defined('MAILER_DSN') ? MAILER_DSN : \getenv('MAILER_DSN');
}

function mailerdsn_parse_url($url)
{
    $config = \parse_url($url);

    if (false === $config || !isset($config['scheme']) || !isset($config['host'])) {
        throw new \RuntimeException(
            \sprintf('Mailformed mail URL: "%s".', $url)
        );
    }

    if (isset($config['query'])) {
        \parse_str($config['query'], $config['query']);
    }

    return $config;
}

function mailerdsn_phpmailer_configure($phpmailer, $url)
{
    $config = \mailerdsn_parse_url($url);

    switch ($config['scheme']) {
        case 'mail':
            $phpmailer->isMail();
            break;
        case 'sendmail':
            $phpmailer->isSendmail();
            break;
        case 'qmail':
            $phpmailer->isQmail();
            break;
        case 'smtp':
        case 'smtps':
            \mailerdsn_phpmailer_configure_smtp($phpmailer, $config);
            break;
        default:
            throw new \RuntimeException(
                \sprintf(
                    'Invalid mail URL scheme: "%s". Allowed values: "mail", "sendmail", "qmail", "smtp", "smtps".',
                    $config['scheme'],
                )
            );
    }

    if (isset($config['query'])) {
        \mailerdsn_phpmailer_configure_options($phpmailer, $config['query']);
    }

    return $phpmailer;
}

function mailerdsn_phpmailer_configure_smtp($phpmailer, $config)
{
    $phpmailer->isSMTP();
    $isSMTPS = 'smtps' === $config['scheme'];

    if ($isSMTPS) {
        $phpmailer->SMTPSecure = 'tls';
    }

    $phpmailer->Host = $config['host'] ?? 'localhost';
    $phpmailer->Port = $config['port'] ?? ($isSMTPS ? 587 : 25);

    if (isset($config['user']) || isset($config['pass'])) {
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $config['user'] ?? '';
        $phpmailer->Password = $config['pass'] ?? '';
    } else {
        $phpmailer->SMTPAuth = false;
    }
}

function mailerdsn_phpmailer_configure_options($phpmailer, $options)
{
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
                    \implode('", "', $allowedOptions)
                )
            );
        }

        switch ($key) {
            case 'AllowEmpty':
            case 'SMTPAutoTLS':
            case 'SMTPKeepAlive':
            case 'SingleTo':
            case 'UseSendmailOptions':
            case 'do_verp':
            case 'DKIM_copyHeaderFields':
                $phpmailer->$key = (bool) $value;
                break;
            case 'Priority':
            case 'SMTPDebug':
            case 'WordWrap':
                $phpmailer->$key = (integer) $value;
                break;
            default:
                $phpmailer->$key = $value;
                break;
        }
    }
}
