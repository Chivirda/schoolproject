<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app.login")
     */
    public function login(): Response
    {
        return $this->render('app/login.html.twig');
    }

    /**
     * @Route("/logout", name="app.logout")
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('index');
    }
}