<?php

namespace App\Controller;

use App\Exception\RegistrationMailSendFailedException;
use App\Repository\UserRepository;
use App\Service\RegistrationEmailSender;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route(path="/send-email", name="app.send_email")
     */
    public function indexAction(RegistrationEmailSender $emailSender, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        try {
            $emailSender->sendSuccessUserRegistration($user);
        } catch (RegistrationMailSendFailedException $e) {
            $this->logger->info($e->getMessage());
            return new Response('Письмо не отправлено.');
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            return new Response('Письмо не отправлено.');
        }

        return new Response('Письмо успешно отправлено.');
    }
}