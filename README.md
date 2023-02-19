# WordPress Mail URL 

[![CI](https://github.com/voronkovich/wordpress-mail-url/actions/workflows/ci.yml/badge.svg)](https://github.com/voronkovich/wordpress-mail-url/actions/workflows/ci.yml)

[WordPress](https://wordpress.org/) plugin to configure [wp_mail()](https://developer.wordpress.org/reference/functions/wp_mail/) via `MAIL_URL` environment variable.

## Installation

Use the [Composer](https://getcomposer.org/):

```sh
composer require voronkovich/wordpress-mail-url
```

Don't forget to activate the plugin, if you don't use the `mu-plugins` directory.

Define (in your `.env` file for example) the `MAIL_URL` variable like this:
```sh
MAIL_URL='mail://localhost'
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
MAIL_URL='mail://localhost?XMailer=SuperMailer&FromName=CoolSite'
```

[PHPMailer](https://github.com/PHPMailer/PHPMailer) configured by public properties, so you can use any of them. All allowed options could be found at [PHPMailer Docs](https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#toc-properties).

## Examples

### Sendmail
```sh
MAIL_URL='sendmail://localhost?Sendmail=/usr/sbin/sendmail%20-oi%20-t'
```

### SMTP
```sh
MAIL_URL='smtp://user@password@localhost?SMTPDebug=3&Timeout=1000'
```

### Gmail
```sh
MAIL_URL='smtps://user@gmail.com:password@smtp.gmail.com?SMTPDebug=3'
```

## License

Copyright (c) Voronkovich Oleg. Distributed under the [MIT](LICENSE).
