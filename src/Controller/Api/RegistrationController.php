<?php

namespace App\Controller\Api;

use App\DTO\UserDto;
use App\Form\Type\RegistrationType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="api.registration")
     */
    public function registrationAction(
        Request $request,
        UserManager $userManager
    ): Response {
        if ($this->getUser()) {
            return $this->json([
                'error'   => true,
                'message' => 'Пользователь уже авторизован.',
            ]);
        }

        $userDto = new UserDto();

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(RegistrationType::class, $userDto, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (
                    !$userManager->isPlanPasswordAndRepeatedPasswordAreEqual(
                        $userDto->getPlainPassword(),
                        $userDto->getRepeatedPlainPassword()
                    )
                ) {
                    return $this->json([
                        'error'   => true,
                        'message' => 'Пароли не совпадают.',
                    ]);
                } elseif ($userManager->isUserExistByEmail($userDto->getEmail())) {
                    return $this->json([
                        'error'   => true,
                        'message' => 'Пользователь с таким email уже существует.',
                    ]);
                } else {
                    $userManager->registerUser($userDto);

                    return $this->json([
                        'success'   => true
                    ]);
                }
            } else {
                return $this->json([
                    'error'   => true,
                    'message' => (string)$form->getErrors(true, false),
                ]);
            }
        }

        return $this->json([
            'error'   => true,
            'message' => 'Пустой запрос',
        ]);
    }
}