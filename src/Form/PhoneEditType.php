<?php

namespace App\Form;

use App\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', TextType::class, [
                'label' => 'Номер телефона', // Поле для ввода номера телефона
            ])
            ->add('phones', CollectionType::class, [
                'entry_type' => TextType::class, // Тип элементов коллекции (текстовое поле)
                'allow_add' => true, // Разрешение на добавление новых элементов
                'allow_delete' => true, // Разрешение на удаление элементов
                'prototype' => true, // Включение прототипа для динамического добавления элементов
                'by_reference' => false, // Изменения применяются напрямую к объекту
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Phone::class, // Указываем, что форма связана с сущностью Phone
        ]);
    }
}