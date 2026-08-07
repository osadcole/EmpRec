<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('password')
            ->add('validTo',DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => false,
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => fn(Employee $employee) => $employee->getName().' '.$employee->getSurname(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
