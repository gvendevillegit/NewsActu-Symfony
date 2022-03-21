<?php

namespace App\Form;

use App\Entity\Commentary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('comment', TextareaType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Commentez ici cet article'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le commentaire ne peut être vide'
                ])
            ],
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Commenter <i class="fas fa-paper-plane"></i>',
            'attr' => [
                'class' => 'd-block col-3 my-3 mx-auto btn btn-warning'
            ],
            'validate' => false,
            'label_html' => true,
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentary::class,
        ]);
    }
}
