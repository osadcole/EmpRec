<?php

namespace App\Form;

use App\Entity\Role;
use App\Service\EmployeeSort;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('surname', TextType::class, [
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'required' => false
            ])
            ->add('email', TextType::class, [
                'required' => false
            ])
            ->add('web', TextType::class, [
                'required' => false
            ])
            ->add('room', TextType::class, [
                'required' => false,
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'title',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'autocomplete'
                ],
            ])
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'choices' => array_combine(EmployeeSort::values(), EmployeeSort::values()),
            ])
            ->add('sortDirection', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'ASC' => 'ASC',
                    'DESC' => 'DESC',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
