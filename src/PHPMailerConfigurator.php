<?php

declare(strict_types=1);

namespace PopArtDesign\WordPressMailerDSN;

use PHPMailer\PHPMailer\DSNConfigurator;
use PHPMailer\PHPMailer\PHPMailer;

use function add_action;
use function constant;
use function defined;
use function explode;
use function getenv;

/**
 * Configures PHPMailer via environment variables.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class PHPMailerConfigurator
{
    private $dsnConfigurator;

    public function __construct()
    {
        $this->dsnConfigurator = new DSNConfigurator();
    }

    public function configure(PHPMailer $mailer): void
    {
        $this->configureDSN($mailer);
        $this->configureDebug($mailer);
        $this->configureCommon($mailer);
        $this->configureDKIM($mailer);
    }

    private function configureDSN(PHPMailer $mailer): void
    {
        if (!$dsn = $this->getConfig('MAILER_DSN')) {
            return;
        }

        $this->dsnConfigurator->configure($mailer, $dsn);
    }

    private function configureDebug(PHPMailer $mailer): void
    {
        if ($debug = $this->getConfig('MAILER_DEBUG')) {
            $mailer->SMTPDebug = (int) $debug;
        }

        if ($debugOutput = $this->getConfig('MAILER_DEBUG_OUTPUT')) {
            $mailer->Debugoutput = $debugOutput;
        }
    }

    private function configureCommon(PHPMailer $mailer): void
    {
        if ($from = $this->getConfig('MAILER_FROM')) {
            $mailer->From = $from;
        }

        if ($fromName = $this->getConfig('MAILER_FROM_NAME')) {
            $mailer->FromName = $fromName;
        }

        if ($sender = $this->getConfig('MAILER_SENDER')) {
            $mailer->Sender = $sender;
        }
    }

    private function configureDKIM(PHPMailer $mailer): void
    {
        if ($private = $this->getConfig('MAILER_DKIM_PRIVATE')) {
            $mailer->DKIM_private = $private;
        }

        if ($privateString = $this->getConfig('MAILER_DKIM_PRIVATE_STRING')) {
            $mailer->DKIM_private_string = $privateString;
        }

        if ($passphrase = $this->getConfig('MAILER_DKIM_PASSPHRASE')) {
            $mailer->DKIM_passphrase = $passphrase;
        }

        if ($selector = $this->getConfig('MAILER_DKIM_SELECTOR')) {
            $mailer->DKIM_selector = $selector;
        }

        if ($identity = $this->getConfig('MAILER_DKIM_IDENTITY', $mailer->From)) {
            $mailer->DKIM_identity = $identity;
        }

        if ($domain = $this->getConfig('MAILER_DKIM_DOMAIN', explode('@', $identity)[1] ?? null)) {
            $mailer->DKIM_domain = $domain;
        }
    }

    private function getConfig(string $key, $default = null)
    {
        if ($value = getenv($key)) {
            return $value;
        }

        return defined($key) ? constant($key) : $default;
    }
}
