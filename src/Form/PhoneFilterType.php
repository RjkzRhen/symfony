<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone', TextType::class, [
                'label' => 'Фильтр по номеру телефона', // Поле для фильтрации по номеру телефона
                'required' => false, // Поле не обязательно для заполнения
                'attr' => ['placeholder' => 'Введите номер телефона'], // Плейсхолдер для поля
            ])
            ->add('user', TextType::class, [
                'label' => 'Фильтр по имени пользователя', // Поле для фильтрации по имени пользователя
                'required' => false, // Поле не обязательно для заполнения
                'attr' => ['placeholder' => 'Введите имя пользователя'], // Плейсхолдер для поля
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', // Метод отправки формы (GET)
            'csrf_protection' => false, // Отключение защиты CSRF
        ]);
    }
}