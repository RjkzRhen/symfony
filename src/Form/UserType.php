<?php

namespace App\Form; // Определяем пространство имен для формы

use App\Entity\User; // Подключаем сущность User
use Symfony\Component\Form\AbstractType; // Подключаем базовый класс для форм
use Symfony\Component\Form\FormBuilderInterface; // Подключаем интерфейс FormBuilderInterface
use Symfony\Component\OptionsResolver\OptionsResolver; // Подключаем класс OptionsResolver для настройки формы
use Symfony\Component\Form\Extension\Core\Type\TextType; // Подключаем тип поля TextType
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Подключаем тип поля IntegerType
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Подключаем тип поля PasswordType


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Имя'
            ])
            ->add('middleName', TextType::class, [
                'label' => 'Отчество',
                'required' => false
            ])
            ->add('age', IntegerType::class, [ // Добавляем поле age
                'label' => 'Возраст'
            ])
            ->add('username', TextType::class, [
                'label' => 'Имя пользователя'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}