<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('phoneNumber', TextType::class, [
                'label' => 'Télephone',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'maxlength' => 100
                ]
            ])

            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'attr' => [
                    'maxlength' => 10
                ]
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ]
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Entrez un mot de passe',
                    ),
                    new Length(
                        min: 10,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                    new Regex(
                        pattern : '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                        message : 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.',
                    ),     
                ],
                'help' => 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
            ])

            // // Liste déroulante
            // ->add('role', ChoiceType::class, [
            //     'label' => 'Rôle',
            //     'choices' => [
            //         'Utilisateur' => 'USER_ROLE',
            //         'Administrateur' => 'ADMIN_ROLE'
            //     ],
            //     'expanded' => false, // true = boutons radio
            //     'multiple' => false
            // ])

            // Submit button
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
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
