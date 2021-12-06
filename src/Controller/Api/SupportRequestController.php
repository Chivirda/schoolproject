<?php

namespace App\Controller\Api;

use App\DTO\SupportRequestAnswerDTO;
use App\DTO\SupportRequestDTO;
use App\Entity\SupportRequest as RequestEntity;
use App\Entity\SupportRequestAnswer;
use App\Entity\User;
use App\FilterOptionCollection\GetSupportRequestListFilterOptionCollection;
use App\Form\Type\SupportRequestAnswerType;
use App\Form\Type\SupportRequestType;
use App\Repository\SupportRequestRepository;
use App\Repository\UserRepository;
use App\Service\SupportRequestApiDataManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/request")
 */
class SupportRequestController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/list", name="api.request.list")
     */
    public function getSupportRequestListAction(
        Request $request,
        SupportRequestApiDataManager $supportRequestApiDataManager
    ): Response {
        $optionCollection = GetSupportRequestListFilterOptionCollection::buildFromRequest($request);

        return $this->json([
            'success' => true,
            'data'    => $supportRequestApiDataManager->getSupportRequestDataListByFilterOptionCollection($optionCollection),
        ]);
    }

    /**
     * @Route("/show/{id}", name="api.request.show")
     */
    public function getSupportRequestDataAction(
        Request $request,
        int $id,
        SupportRequestRepository $supportRequestRepository
    ): Response {
        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        return $this->json([
            'success' => true,
            'data'    => $supportRequest->getData(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="api.request.delete")
     */
    public function deleteSupportRequestDataAction(
        Request $request,
        int $id,
        SupportRequestRepository $supportRequestRepository
    ): Response {
        $user = $this->getUserByBearerToken($request);

        if (is_null($user)) {
            return $this->json([
                'error'   => true,
                'message' => 'Пользователь не авторизован',
            ]);
        }

        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        if ($supportRequest->getCreatedBy()->getId() !== $user->getId()) {
            return $this->json([
                'error'   => true,
                'message' => 'Удалить заявку может только создатель',
            ]);
        }

        $this->entityManager->remove($supportRequest);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @Route("/add", name="api.request.add")
     */
    public function addSupportRequestAction(
        Request $request,
        SupportRequestApiDataManager $supportRequestApiDataManager
    ): Response {
        $user = $this->getUserByBearerToken($request);

        if (is_null($user)) {
            return $this->json([
                'error'   => true,
                'message' => 'Пользователь не авторизован',
            ]);
        }
        $data = json_decode($request->getContent(), true);

        $requestDto = new SupportRequestDTO($user);

        $form = $this->createForm(SupportRequestType::class, $requestDto, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $supportRequest = RequestEntity::createFromDTO($requestDto);
                $this->entityManager->persist($supportRequest);
                $this->entityManager->flush();
                $this->entityManager->refresh($supportRequest);

                return $this->json([
                    'success' => true,
                    'data'    => [
                        'id' => $supportRequest->getId(),
                    ],
                ]);
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

    /**
     * @Route("/edit/{id}", name="api.request.edit")
     */
    public function editAction(
        Request $request,
        int $id,
        SupportRequestRepository $supportRequestRepository
    ): Response {
        $user = $this->getUserByBearerToken($request);

        if (is_null($user)) {
            return $this->json([
                'error'   => true,
                'message' => 'Пользователь не авторизован',
            ]);
        }

        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        if ($supportRequest->getCreatedBy()->getId() !== $user->getId()) {
            return $this->json([
                'error'   => true,
                'message' => 'Удалить заявку может только создатель',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        $requestDto = SupportRequestDTO::createFromEntity($supportRequest);

        $form = $this->createForm(SupportRequestType::class, $requestDto, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $supportRequest->updateFromDTO($requestDto);
                $this->entityManager->flush();

                return $this->json([
                    'success' => true,
                    'data'    => $supportRequest->getData(),
                ]);
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

    /**
     * @Route("/answer/{id}", name="api.request.answer")
     */
    public function answerAction(
        Request $request,
        int $id,
        SupportRequestRepository $supportRequestRepository
    ): Response {
        $user = $this->getUserByBearerToken($request);

        if (is_null($user)) {
            return $this->json([
                'error'   => true,
                'message' => 'Пользователь не авторизован',
            ]);
        }

        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        if (!is_null($supportRequest->getAnswer())) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Ответ для заявки с id %s уже был дан.', $id),
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $answerDto = new SupportRequestAnswerDTO($supportRequest, $user);

        $form = $this->createForm(SupportRequestAnswerType::class, $answerDto, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($data);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $answer = SupportRequestAnswer::createFromDto($answerDto);
                $supportRequest->setStatus($answerDto->getStatus());
                $this->entityManager->persist($answer);
                $this->entityManager->flush();

                return $this->json([
                    'success' => true,
                ]);
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

    /**
     * @Route("/get-answer/{id}", name="request.get_answer")
     */
    public function getAnswerAction(
        Request $request,
        int $id,
        SupportRequestRepository $supportRequestRepository
    ): Response {
        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        return $this->json([
            'success' => true,
            'data'    => $supportRequest->getAnswer() ? $supportRequest->getAnswer()->getData() : null,
        ]);
    }

    private function getUserByBearerToken(Request $request): ?User
    {
        if ($request->headers->has('Authorization')
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ')) {
            $token = substr($request->headers->get('Authorization'), 7);

            return $this->userRepository->getUserByApiKey($token);
        }

        return null;
    }
}