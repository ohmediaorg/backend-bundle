<?php

namespace OHMedia\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiSaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('go_back', SubmitType::class);
        $builder->add('keep_editing', SubmitType::class);
        $builder->add('add_another', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'button_class' => 'btn bt-primary',
        ]);
    }

    public function getParent(): string
    {
        return FormType::class;
    }
}
