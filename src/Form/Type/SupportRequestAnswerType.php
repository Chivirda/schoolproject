<?php

namespace App\Form\Type;

use App\DTO\SupportRequestAnswerDTO;
use App\DTO\SupportRequestDTO;
use App\Entity\SupportRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportRequestAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label'    => 'Статус',
                'choices'  => array_flip(SupportRequest::ANSWER_STATUS_TITLE_LIST),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('answer', TextareaType::class, [
                'label' => 'Сообщение ответа',
            ])
            ->add('save', SubmitType::class, ['label' => 'Дать решение']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportRequestAnswerDTO::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }


}