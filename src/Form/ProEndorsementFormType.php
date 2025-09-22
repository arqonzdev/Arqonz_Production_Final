<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProEndorsementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Your existing fields
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Full name',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Full Name',
                ],
                'required' => true
            ])
            ->add('Email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'E-mail',
                ],
                'required' => true
            ])
            ->add('Phone', TextType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'maxlength' => 190,
                    'placeholder' => 'Mobile',
                ],
                'required' => true
            ])
            ->add('Profession', ChoiceType::class, [
                'label' => sprintf('Your Profession'),
                'choices' => [
                    'Select Profession' => '',
                    'Architect' => 'Architect',
                    'Builder' => 'Builder',
                    'Contractor' => 'Contractor',
                    'Designer' => 'Designer',
                    'Product Supplier' => 'Product Supplier',
                ],
                
                'multiple' => false, // Allow only one choice
                'required' => true,
            ])
            // ->add('Profession', TextType::class, [
            //     'label' => 'Your Profession',
            //     'attr' => [
            //         'maxlength' => 190,
            //         'placeholder' => 'Your Profession',
            //     ],
            //     'required' => true
            // ])
            ->add('q1', ChoiceType::class, [
                'label' => sprintf('How Would you rate the Service Provided by %s', $options['company_name']),
                'choices' => [
                    'Very Good' => 'Very Good',
                    'Good' => 'Good',
                    'Average' => 'Average',
                    'Poor' => 'Poor',
                ],
                'expanded' => true, // This makes the radio buttons instead of a dropdown
                'multiple' => false, // Allow only one choice
                'required' => true,
                'placeholder' => 'Choose an option',
            ])
            ->add('q2', ChoiceType::class, [
                'label' => sprintf('Would you Recommend %s to Others?', $options['company_name']),
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No',
                    'Maybe' => 'Maybe',
                ],
                'expanded' => true, // This makes the radio buttons instead of a dropdown
                'multiple' => false, // Allow only one choice
                'required' => true,
                'placeholder' => 'Choose an option',
            ])
            ->add('q3', ChoiceType::class, [
                'label' => sprintf('How much Would you rate overall Experience with %s', $options['company_name'], "(Out of 5)"),
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
                'expanded' => true, // This makes the radio buttons instead of a dropdown
                'multiple' => false, // Allow only one choice
                'required' => true,
                'placeholder' => 'Choose an option',
            ]);

        // Add submit button
        $builder->add('_submit', SubmitType::class, [
            'label' => 'Submit',
            'attr' => [
                'class' => 'btn btn-lg btn-block btn-success',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('company_name');
    }

}
