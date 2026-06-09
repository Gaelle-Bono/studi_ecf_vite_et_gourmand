<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;


class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // --- Customer section ---
            ->add('customerFirstNameAtOrder', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['maxlength' => 50]
            ])

            ->add('customerLastNameAtOrder', TextType::class, [
                'label' => 'Nom',
                'attr' => ['maxlength' => 50]
            ])

            ->add('customerEmailAtOrder', EmailType::class, [
                'label' => 'Adresse e-mail'
            ])

            ->add('customerPhoneAtOrder', TextType::class, [
                'label' => 'Télephone',
                'attr' => ['maxlength' => 20],
                'help' => "Le numéro de téléphone ne doit contenir que des chiffres, espaces ou +."
            ])

            
            // --- Service section ---
            ->add('serviceDate', DateType::class, [
                'label' => 'Date de la prestation',
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime('+1 day'))->format('Y-m-d'),
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une date de prestation.'
                ]),

                    new Assert\GreaterThanOrEqual([
                        'value' => 'tomorrow',
                        'message' => 'La prestation doit être réservée au minimum pour demain.'
                    ])
                ]
            ])

            ->add('requestedTime', TimeType::class, [
                'label' => 'Heure souhaitée',
                'mapped' => false,
                'required' => false,
                'widget' => 'single_text'
            ])

            ->add('serviceAddress', TextType::class, [
                'label' => 'Adresse de la prestation',
                'attr' => ['maxlength' => 180]
            ])

            ->add('serviceAddressComplement', TextType::class, [
                'label' => 'Complément d’adresse',
                'required' => false,
                'attr' => ['maxlength' => 180],
                'help' => 'Informations complémentaires pour faciliter la livraison (étage, digicode, bâtiment…).'
            ])

            ->add('serviceZipCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['maxlength' => 10]
            ])

            ->add('serviceCity', TextType::class, [
                'label' => 'Ville',
                'attr' => ['maxlength' => 50]
            ])


            // --- Order section ---
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => 'title',
                'label' => 'Choix du menu',
                'placeholder' => 'Choisir un menu',
            ])

            ->add('numberOfPeople', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'attr' => ['min' => 1]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}