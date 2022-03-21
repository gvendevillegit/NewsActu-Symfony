<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre eMail'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Choisissez un mot de passe'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom'
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Je m'inscris",
                'validate' => false,
                'attr' => [
                    'class' => 'd-block col-2 my-3 mx-auto btn btn-warning'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
