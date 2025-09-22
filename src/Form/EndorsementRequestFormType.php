<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class EndorsementRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Your existing fields
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
            ->add('Phone', TextType::class, [
                'label' => 'Phone',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Mobile',
                ],
                'required' => true
            ])
            ->add('Profession', ChoiceType::class, [
                'label' => 'Profession',
                'required' => true,
                'choices' => [
                    'Select Profession' => '',
                    'Architect' => 'Architect',
                    'Builder' => 'Builder',
                    'Contractor' => 'Contractor',
                    'Designer' => 'Designer',
                    'Engineer' => 'Engineer',
                    'Manufacturer' => 'Manufacturer',
                    'Dealer' => 'Dealer',
                    'Distributor' => 'Distributor',
                    'Engineer' => 'Engineer',
                    'Retailer' => 'Retailer',  
                ],
                'attr' => [
                    'placeholder' => 'Select Profession',
                ],
            ]);

        // Add submit button
        $builder->add('_submit', SubmitType::class, [
            'label' => 'Submit',
            'attr' => [
                'class' => 'btn btn-lg btn-block btn-success',
            ],
        ]);
    }

}
