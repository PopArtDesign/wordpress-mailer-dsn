# WordPress Mailer DSN (Data Source Name)

[WordPress](https://wordpress.org/) plugin to configure [wp_mail()](https://developer.wordpress.org/reference/functions/wp_mail/) via `MAILER_DSN` environment variable.

## Installation

Use the [Composer](https://getcomposer.org/):

```sh
composer require voronkovich/wordpress-mailer-dsn
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
MAILER_DSN='smtps://user@gmail.com:password@smtp.gmail.com?SMTPDebug=3'
```

## License

Copyright (c) Voronkovich Oleg. Distributed under the [MIT](LICENSE).
