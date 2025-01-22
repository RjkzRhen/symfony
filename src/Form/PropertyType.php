<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название', // Поле для ввода названия объекта недвижимости
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание', // Поле для ввода описания объекта недвижимости
            ])
            ->add('price', TextType::class, [
                'label' => 'Цена', // Поле для ввода цены объекта недвижимости
            ])
            ->add('rooms', TextType::class, [
                'label' => 'Количество комнат', // Поле для ввода количества комнат
            ])
            ->add('area', TextType::class, [
                'label' => 'Площадь', // Поле для ввода площади объекта недвижимости
            ])
            ->add('address', TextType::class, [ // Добавлено поле address
                'label' => 'Адрес', // Поле для ввода адреса объекта недвижимости
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Изображение', // Поле для загрузки изображения
                'required' => false, // Поле не обязательно для заполнения
                'allow_delete' => true, // Разрешение на удаление изображения
                'download_uri' => false, // Отключение ссылки на скачивание
                'image_uri' => true, // Включение отображения изображения
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class, // Указываем, что форма связана с сущностью Property
        ]);
    }
}