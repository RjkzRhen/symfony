<?php

namespace App\Form;

use App\Entity\EmployeeDirectory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EmployeeDirectoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Last Name', // Поле для ввода фамилии сотрудника
                'required' => true, // Поле обязательно для заполнения
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name', // Поле для ввода имени сотрудника
                'required' => true, // Поле обязательно для заполнения
            ])
            ->add('middleName', TextType::class, [
                'label' => 'Middle Name', // Поле для ввода отчества сотрудника
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('position', TextType::class, [
                'label' => 'Position', // Поле для ввода должности сотрудника
                'required' => true, // Поле обязательно для заполнения
            ])
            ->add('telegramId', IntegerType::class, [
                'label' => 'Telegram ID', // Поле для ввода ID Telegram сотрудника
                'required' => false, // Поле не обязательно для заполнения
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmployeeDirectory::class, // Указываем, что форма связана с сущностью EmployeeDirectory
        ]);
    }
}