<?php

namespace App\Form;

use App\Constant\AppConstant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Entrez votre mot de passe actuel'
                    ),
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,

                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'help' => 'Le mot de passe doit contenir au moins ' . AppConstant::MIN_PASSWORD_LENGTH . ' caractères, une minuscule, une majuscule, un chiffre et un caractère spécial',
                ],

                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                ],

                'invalid_message' => 'Les champs de mot de passe doivent correspondre',

                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Entrez un nouveau mot de passe'
                    ),
                    new Assert\Length(
                        min: AppConstant::MIN_PASSWORD_LENGTH,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        max: 4096
                    ),
                    new Assert\Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                        message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial'
                    ),
                ],
            ])
        ;
    }
}