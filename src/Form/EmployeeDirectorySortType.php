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
                'label' => 'Sort By', // Поле для выбора поля сортировки
                'choices' => [
                    'Last Name' => 'lastName', // Сортировка по фамилии
                    'First Name' => 'firstName', // Сортировка по имени
                    'Middle Name' => 'middleName', // Сортировка по отчеству
                    'Position' => 'position', // Сортировка по должности
                    'Telegram ID' => 'telegramId', // Сортировка по ID Telegram
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