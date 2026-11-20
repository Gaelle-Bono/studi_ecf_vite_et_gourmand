<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Dish;
use App\Entity\Diet;
use App\Entity\Theme;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MenuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title', TextType::class, [
                'label' => 'Nom du menu',
                'attr' => ['maxlength' => 100]
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4]
            ])

            ->add('minimumNumberOfPeople', IntegerType::class, [
                'label' => 'Nombre minimum de personnes',
                'attr' => ['min' => 1]
            ])

            ->add('pricePerPerson', MoneyType::class, [
                'label' => 'Prix par personne (en €)',
                'currency' => null,
                'input' => 'string',
                'scale' => 2
            ])

            ->add('remainingQuantity', IntegerType::class, [
                'label' => 'Stock restant',
                'attr' => ['min' => 0]
            ])

            ->add('conditionsText', TextareaType::class, [
                'label' => 'Conditions particulières',
                'required' => false,
                'mapped' => false,
                'help' => 'Saisissez une condition par ligne',
                'attr' => ['rows' => 4]
            ])

            ->add('minimumDaysBeforeOrder', IntegerType::class, [
                'label' => 'Délai minimum de réservation (en jours)',
                'attr' => ['min' => 1]
            ])

            ->add('starter', EntityType::class, [
                'class' => Dish::class,
                'choice_label' => 'title',
                'label' => 'Entrée',
                'placeholder' => 'Aucune entrée',
                'required' => false
            ])

            ->add('mainCourse', EntityType::class, [
                'class' => Dish::class,
                'choice_label' => 'title',
                'label' => 'Plat principal',
                'placeholder' => 'Choisir un plat principal',
                'required' => true,
                'invalid_message' => 'Veuillez choisir un plat principal'
            ])

            ->add('dessert', EntityType::class, [
                'class' => Dish::class,
                'choice_label' => 'title',
                'label' => 'Dessert',
                'placeholder' => 'Aucun dessert',
                'required' => false
            ])

            ->add('diet', EntityType::class, [
                'class' => Diet::class,
                'choice_label' => 'name',
                'label' => 'Régime alimentaire',
                'placeholder' => 'Choisir un régime'
            ])

            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'label' => 'Thème',
                'placeholder' => 'Choisir un thème'
            ])

            ->add('requiresEquipmentLoan', CheckboxType::class, [
                'label' => 'Prêt de matériel nécessaire',
                'required' => false,
                'attr' => [
                    'data-equipment-loan-target' => 'checkbox',
                    'data-action' => 'change->equipment-loan#toggle'
                ]
            ])

            ->add('includedEquipmentDescription', TextType::class, [
                'label' => 'Description du matériel prêté',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'data-equipment-loan-target' => 'includedEquipmentDescription'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class
        ]);
    }
}
