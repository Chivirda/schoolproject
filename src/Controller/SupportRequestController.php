<?php

namespace App\Controller;

use App\DTO\SupportRequestAnswerDTO;
use App\DTO\SupportRequestDTO;
use App\Entity\SupportRequest;
use App\Entity\SupportRequestAnswer;
use App\Form\Type\SupportRequestAnswerType;
use App\Form\Type\SupportRequestType;
use App\Repository\SupportRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/list", name="request.list")
     */
    public function getListAction(SupportRequestRepository $requestRepository): Response
    {
        $requestList = $requestRepository->findAll();

        return $this->render('support-request/list.html.twig', [
            'requestList' => $requestList,
        ]);
    }

    /**
     * @Route("/show/{id}", name="request.show")
     */
    public function showAction(int $id, SupportRequestRepository $requestRepository): Response
    {
        $supportRequest = $requestRepository->findById($id);

        if (is_null($supportRequest)) {
            throw new NotFoundHttpException('Заявка не найдена');
        }

        $answeredForm = null;

        if ($supportRequest->isAnswered() === false) {
            $answerDto = new SupportRequestAnswerDTO($supportRequest, $this->getUser());

            $answeredForm = $this->createForm(SupportRequestAnswerType::class, $answerDto, [
                'action' => $this->generateUrl('request.answer', ['id' => $supportRequest->getId()]),
            ]);
        }

        return $this->renderForm('support-request/show.html.twig', [
            'request'      => $supportRequest,
            'answeredForm' => $answeredForm,
        ]);
    }

    /**
     * @Route("/answer/{id}", name="request.answer")
     */
    public function answerAction(Request $request, SupportRequest $supportRequest): Response
    {
        $answerDto = new SupportRequestAnswerDTO($supportRequest, $this->getUser());

        $form = $this->createForm(SupportRequestAnswerType::class, $answerDto, [
            'action' => $this->generateUrl('request.answer', ['id' => $supportRequest->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer = SupportRequestAnswer::createFromDto($answerDto);
            $supportRequest->setStatus($answerDto->getStatus());
            $this->entityManager->persist($answer);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('request.show', ['id' => $supportRequest->getId()]);
    }

    /**
     * @Route("/add", name="request.add")
     */
    public function addAction(Request $request): Response
    {
        $requestDto = new SupportRequestDTO($this->getUser());

        $form = $this->createForm(SupportRequestType::class, $requestDto, [
            'action' => $this->generateUrl('request.add'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supportRequest = SupportRequest::createFromDTO($requestDto);
            $this->entityManager->persist($supportRequest);
            $this->entityManager->flush();

            return $this->redirectToRoute('request.show', [
                'id' => $supportRequest->getId(),
            ]);
        }

        return $this->renderForm('support-request/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="request.edit")
     */
    public function editAction(Request $request, SupportRequest $supportRequest): Response
    {
        $requestDto = SupportRequestDTO::createFromEntity($supportRequest);

        $form = $this->createForm(SupportRequestType::class, $requestDto, [
            'action' => $this->generateUrl('request.add'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supportRequest->updateFromDTO($requestDto);
            $this->entityManager->flush();

            return $this->redirectToRoute('request.show', [
                'id' => $supportRequest->getId(),
            ]);
        }

        return $this->renderForm('support-request/add.html.twig', [
            'form' => $form,
        ]);
    }
}