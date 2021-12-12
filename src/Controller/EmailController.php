<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\RegistrationEmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route(path="/send-email", name="app.send_email")
     */
    public function indexAction(RegistrationEmailSender $emailSender, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        try {
            $emailSender->sendSuccessUserRegistration($user);
        } catch (TransportExceptionInterface $e) {
            return new Response(sprintf('Не удалось отправить письмо: %s', $e->getMessage()));
        }

        return new Response('Письмо успешно отправлено.');
    }
}