<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeDirectorySortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortBy', ChoiceType::class, [
                'label' => 'Sort By',
                'choices' => [
                    'Last Name' => 'lastName',
                    'First Name' => 'firstName',
                    'Middle Name' => 'middleName',
                    'Position' => 'position',
                    'Telegram ID' => 'telegramId',
                ],
                'required' => false,
            ])
            ->add('sortOrder', ChoiceType::class, [
                'label' => 'Sort Order',
                'choices' => [
                    'Ascending' => 'ASC',
                    'Descending' => 'DESC',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}