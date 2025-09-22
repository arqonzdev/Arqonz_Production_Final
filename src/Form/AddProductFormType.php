<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AddProductFormType extends AbstractType
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

        $builder
            ->add('ProductName', TextType::class, [
                'label' => 'Product Name: ',
                'attr' => [
                    'maxlength' => 190,
                ],
                'required' => true,
            ])
        
            ->add('ProductBrand', TextType::class, [
                'label' => 'Product Brand: ',
                'attr' => [
                    'maxlength' => 190,
                ],
                'required' => true,
            ])
            ->add('InternationalBrand', CheckboxType::class, [
                'label' => 'International Brand?',
                'required' => false,
            ])
            ->add('ProductMaterial', TextAreaType::class, [
                'label' => 'Product Material: ',
                'attr' => [
                    'maxlength' => 190,
                    'title' => 'Add Materials separated by comma (,)',
                ],
                'required' => true,
            ])
            ->add('ProductPrice', TextType::class, [
                'label' => 'Product Price: ',
                'attr' => [
                    'maxlength' => 190,
                ],
                'required' => true,
            ])
            ->add('ProductUnit', TextType::class, [
                'label' => 'Product Unit: ',
                'attr' => [
                    'maxlength' => 190,
                ],
                'required' => true,
            ])

            ->add('ProductDescription', TextareaType::class, [
                'label' => 'Product Description: ',
                'attr' => [
                    'maxlength' => 1000,
                    'style' => 'height: 150px;',
                ],
                'required' => true,
            ])
            ->add('ProductDescription', TextareaType::class, [
                'label' => 'Product Description: ',
                'attr' => [
                    'maxlength' => 1000,
                    'style' => 'height: 150px;',
                ],
                'required' => true,
            ])
            ->add('ProductImage', FileType::class, [
                'label' => 'Display Picture:',
                'attr' => [
                    'placeholder' => '',
                ],
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3000k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (png, jpg, jpeg, gif)',
                    ]),
                ],
                'help' => 'Maximum file size: 300KB. Allowed file types: png, jpg, jpeg, gif.',
            ])
            ->add('ProductTags', TextAreaType::class, [
                'label' => 'Product Tags: ',
                'attr' => [
                    'maxlength' => 190,
                    'title' => 'Add keywords separated by comma (,) which users are likely to search to find this product',
                ],
                'required' => true,
            ])
            ->add('Specifications', TextareaType::class, [
                'label' => 'Specifications: ',
                'attr' => [
                    'maxlength' => 500,
                ],
                'required' => false,
            ])
            // ->add('categories', ChoiceType::class, [
            //     'label' => 'Product Categories',
            //     'attr' => [
            //         'class' => 'select2',
            //         'data-placeholder' => 'Select categories',
            //         'multiple' => true,
            //     ],
            //     'choices' => $this->getCategoriesChoices(), // Replace with your actual method to fetch categories
            //     'required' => false,
            // ])
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
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => $subCategoryChoices,
            ])
            ->add('SubSubCategory', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'required' => false,
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

    // private function getCategoriesChoices(): array
    // {
    //     // Fetch categories from your JSON file or any other source
    //     // You can use file_get_contents() or any other method to get the JSON content
    //     $categoriesJson = file_get_contents('static/files/Product-Categories.json');
    //     $categoriesData = json_decode($categoriesJson, true);

    //     // Check if the JSON is structured correctly
    //     if (!is_array($categoriesData) || !isset($categoriesData['categories']) || !is_array($categoriesData['categories'])) {
    //         return [];
    //     }

    //     // Format data for choices
    //     $choices = [];
    //     foreach ($categoriesData['categories'] as $category) {
    //         $choices[$category['name']] = $category['name'];

    //         if (isset($category['subcategories']) && is_array($category['subcategories'])) {
    //             foreach ($category['subcategories'] as $subcategory) {
    //                 $subKey = $category['name'] . ' - ' . $subcategory['name'];
    //                 $choices[$subKey] = $subcategory['name'];

    //                 if (isset($subcategory['subcategories']) && is_array($subcategory['subcategories'])) {
    //                     foreach ($subcategory['subcategories'] as $subSubcategory) {
    //                         $subSubKey = $subKey . ' - ' . $subSubcategory['name'];
    //                         $choices[$subSubKey] = $subSubcategory['name'];
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return $choices;
    // }

    // private function transformCategoriesData($value): string
    // {
    //     if (!is_array($value)) {
    //         return '';
    //     }

    //     return implode(', ', $value);
    // }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
             // You can add default options here if needed
        ]);
    }
}