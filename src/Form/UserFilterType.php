<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filterField', ChoiceType::class, [
                'label' => 'Filter By', // Поле для выбора поля фильтрации
                'choices' => [
                    'Last Name' => 'lastName', // Фильтр по фамилии
                    'First Name' => 'firstName', // Фильтр по имени
                    'Middle Name' => 'middleName', // Фильтр по отчеству
                    'Username' => 'username', // Фильтр по имени пользователя
                ],
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('filterValue', TextType::class, [
                'label' => 'Filter Value', // Поле для ввода значения фильтра
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('sortBy', ChoiceType::class, [
                'label' => 'Sort By', // Поле для выбора поля сортировки
                'choices' => [
                    'Last Name' => 'lastName', // Сортировка по фамилии
                    'First Name' => 'firstName', // Сортировка по имени
                    'Middle Name' => 'middleName', // Сортировка по отчеству
                    'Username' => 'username', // Сортировка по имени пользователя
                ],
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('sortOrder', ChoiceType::class, [
                'label' => 'Sort Order', // Поле для выбора порядка сортировки
                'choices' => [
                    'Ascending' => 'ASC', // Сортировка по возрастанию
                    'Descending' => 'DESC', // Сортировка по убыванию
                ],
                'required' => false, // Поле не обязательно для заполнения
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