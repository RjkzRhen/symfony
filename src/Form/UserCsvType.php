<?php

namespace App\Form; // Определяем пространство имен для формы

use App\Entity\UserCsv; // Подключаем сущность UserCsv
use Symfony\Component\Form\AbstractType; // Подключаем базовый класс для форм
use Symfony\Component\Form\FormBuilderInterface; // Подключаем интерфейс FormBuilderInterface
use Symfony\Component\Form\Extension\Core\Type\TextType; // Подключаем тип поля TextType
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Подключаем тип поля IntegerType
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Подключаем тип поля PasswordType
use Symfony\Component\OptionsResolver\OptionsResolver; // Подключаем класс OptionsResolver для настройки формы


class UserCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Last_Name', TextType::class, ['label' => 'Фамилия'])
            ->add('First_Name', TextType::class, ['label' => 'Имя'])
            ->add('Middle_Name', TextType::class, ['label' => 'Отчество', 'required' => false])
            ->add('Age', IntegerType::class, ['label' => 'Возраст'])
            ->add('Username', TextType::class, ['label' => 'Имя пользователя'])
            ->add('Password', PasswordType::class, ['label' => 'Пароль']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCsv::class,
        ]);
    }
}