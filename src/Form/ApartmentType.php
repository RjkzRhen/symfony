<?php

namespace App\Form;

use App\Entity\Apartment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apartmentNumber', TextType::class, [
                'label' => 'Номер квартиры', // Поле для ввода номера квартиры
            ])
            ->add('ownerName', TextType::class, [
                'label' => 'ФИО владельца', // Поле для ввода ФИО владельца квартиры
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Номер телефона', // Поле для ввода номера телефона владельца
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('intercomNumber', TextType::class, [
                'label' => 'Номер домофона', // Поле для ввода номера домофона
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('residentsCount', IntegerType::class, [
                'label' => 'Количество проживающих', // Поле для ввода количества проживающих
                'required' => false, // Поле не обязательно для заполнения
            ])
            ->add('roomsCount', IntegerType::class, [
                'label' => 'Количество комнат', // Поле для ввода количества комнат
                'required' => false, // Поле не обязательно для заполнения
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apartment::class, // Указываем, что форма связана с сущностью Apartment
        ]);
    }
}