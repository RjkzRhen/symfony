<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            ->add('age', IntegerType::class, [
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
            'data_class' => User::class, // Указываем, что форма работает с сущностью User
        ]);
    }
}
