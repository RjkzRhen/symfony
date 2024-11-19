<?php

namespace App\Form; // Определяем пространство имен для формы

use App\Entity\UserCsv; // Подключаем сущность UserCsv
use Symfony\Component\Form\AbstractType; // Подключаем базовый класс для форм
use Symfony\Component\Form\FormBuilderInterface; // Подключаем интерфейс FormBuilderInterface
use Symfony\Component\Form\Extension\Core\Type\TextType; // Подключаем тип поля TextType
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Подключаем тип поля IntegerType
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Подключаем тип поля PasswordType
use Symfony\Component\OptionsResolver\OptionsResolver; // Подключаем класс OptionsResolver для настройки формы

class UserCsvType extends AbstractType // Определяем класс формы, наследующийся от AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void // Определяем метод для построения формы
    {
        $builder // Используем объект $builder для добавления полей в форму
        ->add('lastName', TextType::class, ['label' => 'Фамилия']) // Добавляем поле lastName с типом TextType и меткой "Фамилия"
        ->add('firstName', TextType::class, ['label' => 'Имя']) // Добавляем поле firstName с типом TextType и меткой "Имя"
        ->add('middleName', TextType::class, ['label' => 'Отчество', 'required' => false]) // Добавляем поле middleName с типом TextType, меткой "Отчество" и необязательным значением
        ->add('age', IntegerType::class, ['label' => 'Возраст']) // Добавляем поле age с типом IntegerType и меткой "Возраст"
        ->add('username', TextType::class, ['label' => 'Имя пользователя']) // Добавляем поле username с типом TextType и меткой "Имя пользователя"
        ->add('password', PasswordType::class, ['label' => 'Пароль']); // Добавляем поле password с типом PasswordType и меткой "Пароль"
    }

    public function configureOptions(OptionsResolver $resolver): void // Определяем метод для настройки опций формы
    {
        $resolver->setDefaults([ // Устанавливаем значения по умолчанию для опций
            'data_class' => UserCsv::class, // Указываем, что форма работает с сущностью UserCsv
        ]);
    }
}