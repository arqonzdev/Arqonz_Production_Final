<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductsPageFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   

        $builder
        ->add('min', IntegerType::class, [
            'required' => false,
            'label' => 'MIN',
            'attr' => [
                'placeholder' => 'Min'
            ]
        ])
        ->add('max', IntegerType::class, [
            'required' => false,
            'label' => 'MAX',
            'attr' => [
                'placeholder' => 'Max'
            ]
        ])
        ->add('sort', ChoiceType::class, [
            'choices' => [
                'Default' => 'default',
                'Price (Low to High)' => 'priceLowHigh',
                'Price (High to Low)' => 'priceHighLow',
                // 'Newest Arrivals' => 'newest',
                // 'Popularity' => 'popularity',
                // 'Customer Rating' => 'customerRating'
            ],
            'label' => 'Sort By',
            'required' => false,
            'placeholder' => false,
            'data' => 'default',
        ]);

        $builder
            ->add('_submit', SubmitType::class, [
                'label' => 'Apply',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            ]);
    }

    


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
             // You can add default options here if needed
        ]);
    }
}