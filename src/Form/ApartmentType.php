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
                'label' => 'Номер квартиры',
            ])
            ->add('ownerName', TextType::class, [
                'label' => 'ФИО владельца',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Номер телефона',
                'required' => false,
            ])
            ->add('intercomNumber', TextType::class, [
                'label' => 'Номер домофона',
                'required' => false,
            ])
            ->add('residentsCount', IntegerType::class, [
                'label' => 'Количество проживающих',
                'required' => false,
            ])
            ->add('roomsCount', IntegerType::class, [
                'label' => 'Количество комнат',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apartment::class,
        ]);
    }
}