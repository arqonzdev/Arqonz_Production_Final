<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AddProductFormTESTtype extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Fetch categories data from the provided JSON URL
        $categoriesJson = file_get_contents("http://arqonz.in/static/files/Product-Categories.json");
        $categoriesData = json_decode($categoriesJson, true);

        // Initialize arrays to store choices for each level
        $parentCategoryChoices = [];
        $subCategoryChoices = [];
        $subSubCategoryChoices = [];

        // Loop through categories data to populate choices arrays
        foreach ($categoriesData['categories'] as $parentCategory) {
            $parentCategoryChoices[$parentCategory['name']] = $parentCategory['name'];
            if (isset($parentCategory['subcategories'])) {
                foreach ($parentCategory['subcategories'] as $subCategory) {
                    $subCategoryChoices[$subCategory['name']] = $subCategory['name'];
                    if (isset($subCategory['subcategories'])) {
                        foreach ($subCategory['subcategories'] as $subSubCategory) {
                            $subSubCategoryChoices[$subSubCategory['name']] = $subSubCategory['name'];
                        }
                    }
                }
            }
        }

        // Build form with choices populated from JSON data
        $builder
            ->add('ProductName', TextType::class, [
                'label' => 'Product Name: ',
                'attr' => [
                    'maxlength' => 190,
                ],
                'required' => true,
            ])
            ->add('ParentCategory', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => $parentCategoryChoices,
            ])
            ->add('SubCategory', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => $subCategoryChoices,
            ])
            ->add('SubSubCategory', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => $subSubCategoryChoices,
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Publish',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
             // You can add default options here if needed
        ]);
    }
}
