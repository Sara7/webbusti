<?php

namespace App\Notifier;

use App\Entity\User;
use App\Notifier\Mailer\Mailer;
use App\Repository\UserRepository;
use Twig\Environment;

class AdminNotifier
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
     * @var UserRepository
     */
    private $repository;

    /**
     * UserNotifier constructor.
     *
     * @param Mailer         $mailer
     * @param Environment    $twig
     * @param UserRepository $repository
     */
    public function __construct(Mailer $mailer, Environment $twig, UserRepository $repository)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->repository = $repository;
    }

    /**
     * @param string $mailId
     * @param string $subject
     * @param array  $params
     */
    public function notify(string $mailId, string $subject, array $params = []): void
    {
        $admins = $this->repository->getAdmins();

        if (0 === count($admins)) {
            return;
        }

        foreach ($admins as $admin) {
            $this->notifyOneAdmin($admin, $mailId, $subject, $params);
        }
    }

    /**
     * @param User   $admin
     * @param string $mailId
     * @param string $subject
     * @param array  $params
     *
     * @return bool
     */
    private function notifyOneAdmin(User $admin, string $mailId, string $subject, array $params): bool
    {
        $allParams = array_merge([
            'admin' => $admin,
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
        $mail->setTo($admin->getEmail());

        return $this->mailer->send($mail);
    }
}
