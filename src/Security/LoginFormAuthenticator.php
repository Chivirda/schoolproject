<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private RouterInterface $router;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        RouterInterface $router
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $this->isApiLoginRequest($request) || ($request->isMethod('POST') && $request->getPathInfo() === '/login');
    }

    public function authenticate(Request $request): PassportInterface
    {
        if ($this->isApiLoginRequest($request)) {
            $data = json_decode($request->getContent(), true);

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
        } else {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
        }


        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new UserNotFoundException();
                }

                return $user;
            }),
            new CustomCredentials(function ($credential, User $user) {
                $isValid = $this->passwordHasher->isPasswordValid(
                    $user,
                    $credential
                );

                if (!$isValid) {
                    return new BadCredentialsException();
                }

                return true;
            }, $password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($this->isApiLoginRequest($request)) {
            /** @var User $user */
            $user = $token->getUser();

            return new JsonResponse([
                    'success' => true,
                    'data' =>$user->getApiKey()
                ]
            );
        }

        return new RedirectResponse($this->router->generate('hompage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($this->isApiLoginRequest($request)) {
            return new JsonResponse([
                    'error'   => true,
                    'message' => $exception->getMessage(),
                ]
            );
        }

        return new RedirectResponse($this->router->generate('app.login'));
    }

    private function isApiLoginRequest(Request $request): bool
    {
        return ($request->isMethod('POST') && $request->getPathInfo() === '/api/login');
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
