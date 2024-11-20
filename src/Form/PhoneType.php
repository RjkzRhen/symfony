<?php

namespace App\Form;

use App\Entity\Phone;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('user', EntityType::class, [
'class' => User::class,
'choice_label' => function(User $user) {
return $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getMiddleName();
},
'label' => 'Пользователь'
])
->add('value', TextType::class, [
'label' => 'Номер телефона'
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Phone::class,
]);
}
}
