<?php

declare(strict_types=1);

namespace PopArtDesign\WordPressMailerDSN\Tests;

use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;
use PopArtDesign\WordPressMailerDSN\PHPMailerConfigurator;


/**
 * @covers PHPMailerConfigurator
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PHPMailerConfiguratorTest extends TestCase
{
    public function testConfiguresUsingMailerDsnEnv()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $_SERVER['MAILER_DSN'] = 'smtps://pop:art@popartdesign.ru:587?SMTPDebug=3&Timeout=1000';

        $configurator->configure($mailer);

        $this->assertEquals('smtp', $mailer->Mailer);
        $this->assertEquals('pop', $mailer->Username);
        $this->assertEquals('art', $mailer->Password);
        $this->assertEquals('popartdesign.ru', $mailer->Host);
        $this->assertEquals(587, $mailer->Port);
        $this->assertEquals(3, $mailer->SMTPDebug);
        $this->assertEquals(1000, $mailer->Timeout);
    }

    public function testConfiguresUsingMailerDsnConstant()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        define('MAILER_DSN', 'sendmail://localhost?Sendmail=/usr/sbin/sendmail%20-oi%20-t');

        $configurator->configure($mailer);

        $this->assertEquals('sendmail', $mailer->Mailer);
        $this->assertEquals('/usr/sbin/sendmail -oi -t', $mailer->Sendmail);
    }

    public function testGivesPreferenceToEnvsOverConstants()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $_SERVER['MAILER_DSN'] = 'smtps://pop:art@popartdesign.ru:587?SMTPDebug=3&Timeout=1000';
        define('MAILER_DSN', 'sendmail://localhost?Sendmail=/usr/sbin/sendmail%20-oi%20-t');

        $configurator->configure($mailer);

        $this->assertEquals('smtp', $mailer->Mailer);
    }

    public function testConfiguresDebugUsingEnvs()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $_SERVER['MAILER_DEBUG'] = 3;
        $_SERVER['MAILER_DEBUG_OUTPUT'] = 'error_log';

        $configurator->configure($mailer);

        $this->assertEquals(3, $mailer->SMTPDebug);
        $this->assertEquals('error_log', $mailer->Debugoutput);
    }

    public function testConfiguresCommonUsingEnvs()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $_SERVER['MAILER_FROM'] = 'oleg-voronkovich@yandex.ru';
        $_SERVER['MAILER_FROM_NAME'] = 'Oleg Voronkovich';
        $_SERVER['MAILER_SENDER'] = 'no-reply@popartdesign.ru';

        $configurator->configure($mailer);

        $this->assertEquals('oleg-voronkovich@yandex.ru', $mailer->From);
        $this->assertEquals('Oleg Voronkovich', $mailer->FromName);
        $this->assertEquals('no-reply@popartdesign.ru', $mailer->Sender);
    }

    public function testConfiguresDkimUsingEnvs()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $_SERVER['MAILER_DKIM_PRIVATE'] = '/tmp/private.key';
        $_SERVER['MAILER_DKIM_PRIVATE_STRING'] = 'private';
        $_SERVER['MAILER_DKIM_PASSPHRASE'] = 'secret';
        $_SERVER['MAILER_DKIM_SELECTOR'] = 'mailer';
        $_SERVER['MAILER_DKIM_DOMAIN'] = 'popartdesign.ru';
        $_SERVER['MAILER_DKIM_IDENTITY'] = 'no-reply@popartdesign.ru';

        $configurator->configure($mailer);

        $this->assertEquals('/tmp/private.key', $mailer->DKIM_private);
        $this->assertEquals('private', $mailer->DKIM_private_string);
        $this->assertEquals('secret', $mailer->DKIM_passphrase);
        $this->assertEquals('mailer', $mailer->DKIM_selector);
        $this->assertEquals('no-reply@popartdesign.ru', $mailer->DKIM_identity);
        $this->assertEquals('popartdesign.ru', $mailer->DKIM_domain);
    }

    public function testConfiguresDkimDomainAndIdentityWithDefaultValues()
    {
        $configurator = new PHPMailerConfigurator();
        $mailer = new PHPMailer();

        $mailer->From = 'no-reply@popartdesign.ru';

        $_SERVER['MAILER_DKIM_PRIVATE'] = '/tmp/private.key';
        $_SERVER['MAILER_DKIM_PRIVATE_STRING'] = 'private';
        $_SERVER['MAILER_DKIM_PASSPHRASE'] = 'secret';
        $_SERVER['MAILER_DKIM_SELECTOR'] = 'mailer';

        $configurator->configure($mailer);

        $this->assertEquals('no-reply@popartdesign.ru', $mailer->DKIM_identity);
        $this->assertEquals('popartdesign.ru', $mailer->DKIM_domain);
    }
}
