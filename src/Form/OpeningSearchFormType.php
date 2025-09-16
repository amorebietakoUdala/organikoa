<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpeningSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('dni', null, [
                'disabled' => $readonly,
                'label' => 'openings.dni',
            ])
            ->add('fechaInicio', null, [
                'disabled' => $readonly,
                'label' => 'openings.fechaInicio',
            ])
            ->add('fechaFin', null, [
                'disabled' => $readonly,
                'label' => 'openings.fechaFin',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'readonly' => false,
        ]);
    }
}
