<?php

namespace App\Form;

use App\Entity\MenuItemPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('label');

        $builder->add('amount', MoneyType::class, [
            'currency' => 'CAD',
            'html5' => true,
            'attr' => [
                'min' => '0.01',
                'step' => '0.01',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItemPrice::class,
        ]);
    }
}
