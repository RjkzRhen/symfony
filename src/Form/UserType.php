<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия', // Поле для ввода фамилии пользователя
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Имя', // Поле для ввода имени пользователя
            ])
            ->add('middleName', TextType::class, [
                'label' => 'Отчество', // Поле для ввода отчества пользователя
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Возраст', // Поле для ввода возраста пользователя
            ])
            ->add('username', TextType::class, [
                'label' => 'Имя пользователя', // Поле для ввода имени пользователя
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль', // Поле для ввода пароля
            ]);

        // Если форма создается администратором, добавляем поле ролей
        if ($options['is_admin']) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Пользователь' => 'ROLE_USER', // Роль пользователя
                    'Администратор' => 'ROLE_ADMIN', // Роль администратора
                ],
                'multiple' => false, // Изменяем на false, чтобы выбирать только одну роль
                'expanded' => false, // Изменяем на false, чтобы использовать выпадающий список
                'label' => 'Роль', // Метка для поля выбора роли
                'placeholder' => 'Выберите роль', // Плейсхолдер для поля выбора роли
            ]);

            // Добавляем преобразователь данных для поля roles
            $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesArray) {
                        // Преобразуем массив в строку для отображения
                        return count($rolesArray) ? $rolesArray[0] : null;
                    },
                    function ($rolesString) {
                        // Преобразуем строку обратно в массив
                        return [$rolesString];
                    }
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Указываем, что форма связана с сущностью User
            'is_admin' => false, // По умолчанию форма создается без административных прав
        ]);
    }
}