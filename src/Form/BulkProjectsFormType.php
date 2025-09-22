<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class BulkProjectsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ProfessionalID', TextType::class, [
                'label' => 'Professional ID',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Professional ID',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Professional ID is required'])
                ]
            ])
            ->add('ImageAssetPath', TextType::class, [
                'label' => 'Image Asset Path in Pimcore',
                'required' => true,
                'attr' => [
                    'placeholder' => '/path/to/your/project/images/',
                    'class' => 'form-control'
                ],
                'help' => 'Enter the path where your project images are stored in Pimcore assets (e.g., /pictures/projects/)',
                'constraints' => [
                    new NotBlank(['message' => 'Image asset path is required'])
                ]
            ])
            ->add('ProjectsexcelFile', FileType::class, [
                'label' => 'Upload Projects Excel File',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Excel file (XLS or XLSX)',
                    ]),
                    new NotBlank(['message' => 'Please select an Excel file to upload'])
                ],
                'attr' => [
                    'accept' => '.xls,.xlsx'
                ]
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Upload Projects',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return 'bulk_projects_form';
    }
}
