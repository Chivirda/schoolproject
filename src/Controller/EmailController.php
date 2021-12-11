<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route(path="/send-email", name="app.send_email")
     */
    public function indexAction(Request $request, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('serg@live.com')
            ->to('you@hotmail.com')
            ->subject(sprintf('Hello, %s',
                $request->get('name', 'Unknown')
            ))
            ->text('Lorem ipsum dolor sit amet.')
            ->html(sprintf('<h1>Hello, %s</h1>',
                $request->get('name', 'Unknown')
            ));

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new Response(sprintf('Не удалось отправить письмо: %s', $e->getMessage()));
        }

        return new Response('Письмо успешно отправлено.');
    }
}