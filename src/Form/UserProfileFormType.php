<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['maxlength' => 50],
                'empty_data' => ''
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['maxlength' => 50],
                'empty_data' => ''
            ])

            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['maxlength' => 20],
                'empty_data' => '',
                'help' => "Le numéro de téléphone ne doit contenir que des chiffres, espaces ou +"
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['maxlength' => 180],
                'empty_data' => ''
            ])

            ->add('addressComplement', TextType::class, [
                'label' => 'Complément d\'adresse (optionnel)',
                'attr' => ['maxlength' => 180],
                'required' => false
            ])

            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['maxlength' => 10],
                'empty_data' => ''
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['maxlength' => 50],
                'empty_data' => ''
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