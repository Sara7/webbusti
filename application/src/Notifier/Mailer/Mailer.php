<?php

namespace App\Notifier\Mailer;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $fromAddress;

    /**
     * @var string
     */
    private $fromName;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param string        $fromAddress
     * @param string        $fromName
     */
    public function __construct(\Swift_Mailer $mailer, string $fromAddress, string $fromName)
    {
        $this->mailer = $mailer;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return bool
     */
    public function send(\Swift_Message $message): bool
    {
        $message->setFrom($this->fromAddress, $this->fromName);

        return (bool) $this->mailer->send($message);
    }
}
