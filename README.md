# WordPress Mailer DSN (Data Source Name)

[![CI](https://github.com/PopArtDesign/wordpress-mailer-dsn/actions/workflows/ci.yml/badge.svg)](https://github.com/PopArtDesign/wordpress-mailer-dsn/actions/workflows/ci.yml)

[WordPress](https://wordpress.org/) plugin to configure [wp_mail()](https://developer.wordpress.org/reference/functions/wp_mail/) via `MAILER_DSN` environment variable.

## Installation

Use the [Composer](https://getcomposer.org/):

```sh
composer require popartdesign/wordpress-mailer-dsn
```

Don't forget to activate the plugin, if you don't use the `mu-plugins` directory.

Define (in your `.env` file for example) the `MAILER_DSN` variable like this:
```sh
MAILER_DSN='mail://localhost'
```

## Configuraton

Supported protocols:

- `mail`
- `sendmail`
- `qmail`
- `smtp`
- `smtps`

Additional configuration could be applied via query string:

```sh
MAILER_DSN='mail://localhost?XMailer=SuperMailer&FromName=CoolSite'
```

[PHPMailer](https://github.com/PHPMailer/PHPMailer) configured by public properties, so you can use any of them. All allowed options could be found at [PHPMailer Docs](https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#toc-properties).

## Examples

### Sendmail
```sh
MAILER_DSN='sendmail://localhost?Sendmail=/usr/sbin/sendmail%20-oi%20-t'
```

### SMTP
```sh
MAILER_DSN='smtp://user@password@localhost?SMTPDebug=3&Timeout=1000'
```

### Gmail
```sh
MAILER_DSN='smtps://user@gmail.com:password@smtp.gmail.com:587'
```

## Advanced

Sometimes it's not convinient to configure all options by the only one `MAILER_DSN` variable. For example, if you want to configure [DKIM](https://en.wikipedia.org/wiki/DomainKeys_Identified_Mail), you will end with very long unreadable DSN string. In this cases you can use one of `MAILER_*` variables:

- `MAILER_DEBUG`
- `MAILER_DEBUG_OUTPUT`
- `MAILER_FROM`
- `MAILER_FROM_NAME`
- `MAILER_SENDER`
- `MAILER_DKIM_PRIVATE`
- `MAILER_DKIM_PASSPHRASE`
- `MAILER_DKIM_SELECTOR`
- `MAILER_DKIM_IDENTITY`
- `MAILER_DKIM_DOMAIN`
- and etc.

See source code for all available vars.

## License

Copyright (c) Voronkovich Oleg. Distributed under the [MIT](LICENSE).
