<?php

namespace App\Form;

use App\Constant\AppConstant;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;



class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['maxlength' => 50]
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['maxlength' => 50]
            ])

            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['maxlength' => 20],
                'help' => "Le numéro de téléphone ne doit contenir que des chiffres, espaces ou +"
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['maxlength' => 180]
            ])

            ->add('addressComplement', TextType::class, [
                'label' => 'Complément d\'adresse (optionnel)',
                'attr' => ['maxlength' => 180],
                'required' => false,
            ])

            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['maxlength' => 10]
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['maxlength' => 50]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail'
            ])

           ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,

                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],

                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],

                'invalid_message' => 'Les champs de mot de passe doivent correspondre.',

                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Entrez un mot de passe'
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

                'help' => 'Le mot de passe doit contenir au moins ' . AppConstant::MIN_PASSWORD_LENGTH . ' caractères, une minuscule, une majuscule, un chiffre et un caractère spécial'
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