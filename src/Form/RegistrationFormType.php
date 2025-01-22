<?php

// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Поле для имени
            ->add('firstName', TextType::class, [
                'label' => 'Имя',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите имя.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Имя должно быть не менее {{ limit }} символов.',
                        'max' => 255,
                    ]),
                ],
            ])

            // Поле для фамилии
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите фамилию.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Фамилия должна быть не менее {{ limit }} символов.',
                        'max' => 255,
                    ]),
                ],
            ])

            // Поле для отчества (необязательное)
            ->add('middleName', TextType::class, [
                'label' => 'Отчество',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])

            // Поле для возраста
            ->add('age', IntegerType::class, [
                'label' => 'Возраст',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите возраст.',
                    ]),
                ],
            ])

            // Поле для логина (username)
            ->add('username', TextType::class, [
                'label' => 'Логин',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите логин.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Логин должен быть не менее {{ limit }} символов.',
                        'max' => 180,
                    ]),
                ],
            ])

            // Поле для email
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите email.',
                    ]),
                    new Email([
                        'message' => 'Некорректный email.',
                    ]),
                ],
            ])

            // Поле для пароля
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Пароль',
                'mapped' => false, // Поле не связано с сущностью
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите пароль.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен быть не менее {{ limit }} символов.',
                        'max' => 4096,
                    ]),
                ],
            ])

            // Поле для подтверждения пароля
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Подтверждение пароля',
                'mapped' => false, // Поле не связано с сущностью
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, подтвердите пароль.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Указываем, что форма связана с сущностью User
        ]);
    }
}