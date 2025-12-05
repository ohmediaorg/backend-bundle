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
        if ($options['finish']) {
            $builder->add('finish', SubmitType::class);
        }

        if ($options['keep_editing']) {
            $builder->add('keep_editing', SubmitType::class);
        }

        if ($options['add_another']) {
            $builder->add('add_another', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'button_class' => 'btn bt-primary',
            'mapped' => false,
            'label' => false,
            'row_attr' => [
                'class' => 'fieldset-nostyle mb-3',
            ],
            'finish' => true,
            'keep_editing' => true,
            'add_another' => true,
        ]);
    }

    public function getParent(): string
    {
        return FormType::class;
    }
}
