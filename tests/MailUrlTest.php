<?php

declare(strict_types=1);

namespace Test;

use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

define('ABSPATH', \dirname(__DIR__));

require_once \dirname(__DIR__) . '/functions.php';

/**
 * @coversNothing
 */
class MailUrlTest extends TestCase
{
    public function testThrowsExceptionIfUrlIsMailformed()
    {
        \putenv('MAIL_URL=localhost');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Mailformed mail URL: "localhost".');

        $phpmailer = new PHPMailer(true);

        \mailurl_phpmailer_init($phpmailer);
    }

    public function testThrowsExceptionIfUrHasInvalidScheme()
    {
        \putenv('MAIL_URL=ftp://localhost');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid mail URL scheme: "ftp"');

        $phpmailer = new PHPMailer(true);

        \mailurl_phpmailer_init($phpmailer);
    }

    public function testThrowsExceptionIfUrHasInvalidOption()
    {
        \putenv('MAIL_URL=mail://localhost?Helo=Hi&Unknown=Invalid');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown mail URL option: "Unknown"');

        $phpmailer = new PHPMailer(true);

        \mailurl_phpmailer_init($phpmailer);
    }

    public function testConfiguresPHPMailerWithProvidedByUrlSettings()
    {
        \putenv('MAIL_URL=smtps://user@gmail.com:password@smtp.gmail.com?SMTPDebug=3&Timeout=60');

        $phpmailer = new PHPMailer(true);

        \mailurl_phpmailer_init($phpmailer);

        $this->assertEquals($phpmailer->Mailer, 'smtp');
        $this->assertEquals($phpmailer->Host, 'smtp.gmail.com');
        $this->assertEquals($phpmailer->Port, 587);
        $this->assertEquals($phpmailer->Username, 'user@gmail.com');
        $this->assertEquals($phpmailer->Password, 'password');
        $this->assertEquals($phpmailer->Timeout, 60);
        $this->assertEquals($phpmailer->SMTPDebug, 3);
        $this->assertEquals($phpmailer->SMTPAuth, true);
        $this->assertEquals($phpmailer->SMTPSecure, 'tls');
    }
}
