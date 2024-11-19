<?php

namespace App\Form; // Определяем пространство имен для формы

use App\Entity\User; // Подключаем сущность User
use Symfony\Component\Form\AbstractType; // Подключаем базовый класс для форм
use Symfony\Component\Form\FormBuilderInterface; // Подключаем интерфейс FormBuilderInterface
use Symfony\Component\OptionsResolver\OptionsResolver; // Подключаем класс OptionsResolver для настройки формы
use Symfony\Component\Form\Extension\Core\Type\TextType; // Подключаем тип поля TextType
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Подключаем тип поля IntegerType
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Подключаем тип поля PasswordType

class UserType extends AbstractType // Определяем класс формы, наследующийся от AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void // Определяем метод для построения формы
    {
        $builder // Используем объект $builder для добавления полей в форму
        ->add('lastName', TextType::class, [ // Добавляем поле lastName с типом TextType и меткой "Фамилия"
            'label' => 'Фамилия'
        ])
            ->add('firstName', TextType::class, [ // Добавляем поле firstName с типом TextType и меткой "Имя"
                'label' => 'Имя'
            ])
            ->add('middleName', TextType::class, [ // Добавляем поле middleName с типом TextType, меткой "Отчество" и необязательным значением
                'label' => 'Отчество',
                'required' => false
            ])
            ->add('age', IntegerType::class, [ // Добавляем поле age с типом IntegerType и меткой "Возраст"
                'label' => 'Возраст'
            ])
            ->add('username', TextType::class, [ // Добавляем поле username с типом TextType и меткой "Имя пользователя"
                'label' => 'Имя пользователя'
            ])
            ->add('password', PasswordType::class, [ // Добавляем поле password с типом PasswordType и меткой "Пароль"
                'label' => 'Пароль'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void // Определяем метод для настройки опций формы
    {
        $resolver->setDefaults([ // Устанавливаем значения по умолчанию для опций
            'data_class' => User::class, // Указываем, что форма работает с сущностью User
        ]);
    }
}