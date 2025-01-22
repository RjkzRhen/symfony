<?php

namespace App\Form;

use App\Entity\ArrivalJournalDetail;
use App\Entity\User;
use App\Entity\ExternalRate;
use App\Entity\Counterparty;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ArrivalJournalDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('employee', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Сотрудник',
            ])
            ->add('externalRate', EntityType::class, [
                'class' => ExternalRate::class,
                'choice_label' => 'value',
                'label' => 'Внешняя ставка',
            ])
            ->add('counterparty', EntityType::class, [
                'class' => Counterparty::class,
                'choice_label' => 'name',
                'label' => 'Контрагент',
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => 'name',
                'label' => 'Единица измерения',
            ])
            ->add('value', NumberType::class, [
                'label' => 'Значение',
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Сумма',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArrivalJournalDetail::class,
        ]);
    }
}