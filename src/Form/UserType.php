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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Имя',
            ])
            ->add('middleName', TextType::class, [
                'label' => 'Отчество',
                'required' => false,
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Возраст',
            ])
            ->add('username', TextType::class, [
                'label' => 'Имя пользователя',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль',
            ]);

        // Если форма создается администратором, добавляем поле ролей
        if ($options['is_admin']) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Пользователь' => 'ROLE_USER',
                    'Администратор' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Роли',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_admin' => false, // По умолчанию форма создается без административных прав
        ]);
    }
}