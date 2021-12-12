<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class RegistrationEmailSender
{
    private const SUBJECT_TITLE = 'Вы успешно зарегистрировались!';

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendSuccessUserRegistration(User $user): void
    {
        $preparedEmail = $this->prepareEmail($user);
        $this->mailer->send($preparedEmail);
    }

    private function prepareEmail(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject(self::SUBJECT_TITLE)
            ->htmlTemplate('email/success-registration.html.twig')
            ->context([
                'name' => $user->getFullName(),
                'login' => $user->getEmail(),
            ]);
    }
}