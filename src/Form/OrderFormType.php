<?php

namespace App\Form;
use App\Entity\Menu;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('eventDate')
        ->add('eventTime')
        ->add('address')
        ->add('phone')

        ->add('menu', EntityType::class, [
            'class' => Menu::class,
            'choice_label' => 'title',
            'disabled' => $options['menu_locked'],
            'placeholder' => 'Choisir un menu'
        ])

        ->add('numberOfPeople');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
