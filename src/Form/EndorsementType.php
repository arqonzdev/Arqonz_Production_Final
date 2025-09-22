<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EndorsementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Full name',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Name',
                ],
                'required' => true
            ])
            ->add('Email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Email',
                ],
                'required' => true
            ])
            ->add('Phone', IntegerType::class, [
                'label' => 'Phone',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Mobile',
                ],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
