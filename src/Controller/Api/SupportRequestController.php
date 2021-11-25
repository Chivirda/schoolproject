<?php

namespace App\Controller\Api;

use App\DTO\SupportRequestAnswerDTO;
use App\DTO\SupportRequestDTO;
use App\Entity\SupportRequest as RequestEntity;
use App\Entity\SupportRequestAnswer;
use App\FilterOptionCollection\GetSupportRequestListFilterOptionCollection;
use App\Form\Type\SupportRequestAnswerType;
use App\Form\Type\SupportRequestType;
use App\Repository\SupportRequestRepository;
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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $supportRequest = $supportRequestRepository->findById($id);

        if (is_null($supportRequest)) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Не найдена заявка с id %s.', $id),
            ]);
        }

        if ($supportRequest->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            return $this->json([
                'error'   => true,
                'message' => 'Удалить заявку может только создатель',
            ]);
        }

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
        $requestDto = new SupportRequestDTO($this->getUser());

        $form = $this->createForm(SupportRequestType::class, $requestDto, [
            'csrf_protection' => false,
        ]);

        $data = json_decode($request->getContent(), true);

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
     * @Route("/answer/{id}", name="request.answer")
     */
    public function answerAction(
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

        if (!is_null($supportRequest->getAnswer())) {
            return $this->json([
                'error'   => true,
                'message' => sprintf('Ответ для заявки с id %s уже был дан.', $id),
            ]);
        }

        $answerDto = new SupportRequestAnswerDTO($supportRequest, $this->getUser());

        $form = $this->createForm(SupportRequestAnswerType::class, $answerDto, [
            'csrf_protection' => false,
        ]);

        $data = json_decode($request->getContent(), true);

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
            'data'    => $supportRequest->getAnswer() ? $supportRequest->getAnswer()->getData() : null
        ]);
    }
}