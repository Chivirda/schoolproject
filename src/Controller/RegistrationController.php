<?php

namespace App\Controller;

use App\DTO\UserDto;
use App\Form\Type\RegistrationType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registration")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/", name="app.registration")
     */
    public function registrationAction(
        Request $request,
        UserManager $userManager
    ): Response {
        // Редирект если пользователь авторизирован
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $userDto = new UserDto();

        $form = $this->createForm(RegistrationType::class, $userDto, [
            'action' => $this->generateUrl('app.registration'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (
                !$userManager->isPlanPasswordAndRepeatedPasswordAreEqual(
                    $userDto->getPlainPassword(),
                    $userDto->getRepeatedPlainPassword()
                )
            ) {
                $form->addError(new FormError('Пароли не совпадают.'));
            } elseif ($userManager->isUserExistByEmail($userDto->getEmail())) {
                $form->addError(new FormError('Пользователь с таким email уже существует.'));
            } else {
                $userManager->registerUser($userDto);

                return $this->redirectToRoute('app.success_registration', ['email' => $userDto->getEmail()]);
            }
        }

        return $this->renderForm('app/registration.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/success-registration", name="app.success_registration")
     */
    public function showSuccessRegistrationPageAction(Request $request): Response
    {
        return $this->renderForm('app/success-registration.html.twig', [
            'email' => $request->get('email', ''),
        ]);
    }
}