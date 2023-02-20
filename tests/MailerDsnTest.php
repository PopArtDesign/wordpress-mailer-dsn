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
class MailerDsnTest extends TestCase
{
    public function testThrowsExceptionIfDsnIsMailformed()
    {
        \putenv('MAILER_DSN=localhost');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Mailformed mail DSN: "localhost".');

        $phpmailer = new PHPMailer(true);

        \mailerdsn_phpmailer_init($phpmailer);
    }

    public function testThrowsExceptionIfDsnHasInvalidScheme()
    {
        \putenv('MAILER_DSN=ftp://localhost');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid mail DSN scheme: "ftp"');

        $phpmailer = new PHPMailer(true);

        \mailerdsn_phpmailer_init($phpmailer);
    }

    public function testThrowsExceptionIfDsnHasInvalidOption()
    {
        \putenv('MAILER_DSN=mail://localhost?Helo=Hi&Unknown=Invalid');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown mail DSN option: "Unknown"');

        $phpmailer = new PHPMailer(true);

        \mailerdsn_phpmailer_init($phpmailer);
    }

    public function testConfiguresPHPMailerWithProvidedByDsnSettings()
    {
        \putenv('MAILER_DSN=smtps://user@gmail.com:password@smtp.gmail.com?SMTPDebug=3&Timeout=60');

        $phpmailer = new PHPMailer(true);

        \mailerdsn_phpmailer_init($phpmailer);

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
