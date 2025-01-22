<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tax_rate', NumberType::class, [
                'label' => 'Налоговая ставка (%)', // Поле для ввода налоговой ставки
                'attr' => [
                    'min' => 0, // Минимальное значение
                    'max' => 99.99, // Максимальное значение
                    'step' => 0.01, // Шаг изменения
                    'placeholder' => 'Введите налоговую ставку', // Плейсхолдер
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class, // Указываем, что форма связана с сущностью Setting
        ]);
    }
}
