<?php

namespace App\Notifier;

use App\Entity\User;
use App\Notifier\Mailer\Mailer;
use Twig\Environment;

class UserNotifier
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * UserNotifier constructor.
     *
     * @param Mailer      $mailer
     * @param Environment $twig
     */
    public function __construct(Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User   $user
     * @param string $mailId
     * @param string $subject
     * @param array  $params
     *
     * @return bool
     */
    public function notify(User $user, string $mailId, string $subject, array $params = []): bool
    {
        $allParams = array_merge([
            'user' => $user,
        ], $params);

        try {
            $body = $this->twig->render('/emails/' . $mailId . '.txt.twig', $allParams);
        } catch (\Exception $exception) {
            $body = null;
        }

        try {
            $bodyHtml = $this->twig->render('/emails/' . $mailId . '.html.twig', $allParams);
        } catch (\Exception $exception) {
            return false;
        }

        $mail = new \Swift_Message();
        $mail->setSubject($subject);
        if (null === $body) {
            $mail->setBody($bodyHtml, 'text/html');
        } else {
            $mail->setBody($body);
            $mail->addPart($bodyHtml, 'text/html');
        }
        $mail->setTo($user->getEmail());

        return $this->mailer->send($mail);
    }
}
