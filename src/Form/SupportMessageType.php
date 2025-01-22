<?php
// src/Form/SupportMessageType.php
namespace App\Form;

use App\Entity\SupportMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'Ваше сообщение', // Поле для ввода сообщения в поддержку
                'attr' => ['rows' => 5], // Устанавливаем количество строк в текстовом поле
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportMessage::class, // Указываем, что форма связана с сущностью SupportMessage
        ]);
    }
}