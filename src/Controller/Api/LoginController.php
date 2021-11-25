<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    /**
     * @Route("/login", name="api.login")
     */
    public function loginAction(Request $request): Response
    {

        return new JsonResponse([
            'success' => true,
            'data'    => [
                md5('password'),
            ],
        ]);
    }
}