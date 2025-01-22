<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeDirectoryFilterType extends AbstractType
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
                    'Position' => 'position', // Фильтр по должности
                    'Telegram ID' => 'telegramId', // Фильтр по ID Telegram
                ],
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('filterValue', TextType::class, [
                'label' => 'Filter Value', // Поле для ввода значения фильтра
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