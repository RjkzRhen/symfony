<?php

namespace App\Form;

use App\Entity\Phone;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class, // Указываем сущность User
                'choice_label' => function(User $user) {
                    return $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getMiddleName(); // Отображаем ФИО пользователя
                },
                'label' => 'Пользователь', // Поле для выбора пользователя
                'disabled' => false, // Поле не отключено
            ])
            ->add('phones', CollectionType::class, [
                'entry_type' => TextType::class, // Тип элементов коллекции (текстовое поле)
                'allow_add' => true, // Разрешение на добавление новых элементов
                'allow_delete' => true, // Разрешение на удаление элементов
                'prototype' => true, // Включение прототипа для динамического добавления элементов
                'by_reference' => false, // Изменения применяются напрямую к объекту
                'label' => false, // Убираем метку для поля phones
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Phone::class, // Указываем, что форма связана с сущностью Phone
        ]);
    }
}