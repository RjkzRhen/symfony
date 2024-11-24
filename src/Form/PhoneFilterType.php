<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filterField', ChoiceType::class, [
                'label' => 'Filter By',
                'choices' => [
                    'User' => 'user',
                    'Phone' => 'value',
                ],
                'required' => false,
            ])
            ->add('filterValue', TextType::class, [
                'label' => 'Filter Value',
                'required' => false,
            ])
            ->add('sortBy', ChoiceType::class, [
                'label' => 'Sort By',
                'choices' => [
                    'User' => 'user',
                    'Phone' => 'value',
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
