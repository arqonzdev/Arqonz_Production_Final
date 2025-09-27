<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ImageGallery;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfessionalAddProjectFormType extends AbstractType
{   
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getCitiesChoices(),
            'is_builder' => false,
        ]);
    }

    private function getCitiesChoices(): array
    {
        $jsonUrl = 'https://gist.githubusercontent.com/desaurabh/25a46d20c266b86a054b/raw/e613fe161964d7a668e44bc786b3fad776c33061/Indian-States-&-Cities.json';

        $jsonContent = file_get_contents($jsonUrl);
        $decoder = new JsonDecode();
        $data = $decoder->decode($jsonContent, 'json');

        $choices = [];
        foreach ($data as $state => $cities) {
            $stateChoices = [];
            foreach ($cities as $cityData) {
                $stateChoices[$cityData->city] = $cityData->city;
            }
            $choices[$state] = $stateChoices;
        }

        return $choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isBuilder = $options['is_builder'];
        
        $builder
            ->add('ProjectName', TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'placeholder' => 'Enter Title',
                    'maxlength' => 190
                ],
                'required' => true,
            ])
            ->add('ProjectDescription', TextareaType::class, [
                'label' => 'Project Description',
                'attr' => [
                    'placeholder' => 'Enter Description',
                    'maxlength' => 1000,
                    'style' => 'height: 150px;'
                ],
                'required' => true,
            ])
            ->add('ProjectGallery', FileType::class, [
                'label' => 'Add Project Picture: ',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'image-input',
                    'data-preview-container' => 'image-preview-container',
                ],
                'required' => false,
                'multiple' => 'multiple',
            ])
            ->add('Location', ChoiceType::class, [
                'label' => 'Project Location: ',
                'choices' => $options['choices'],
                'placeholder' => 'Select City',
                'multiple' => false,
                'choice_value' => function ($value) {
                    return $value;
                },
            ]);

        // Add Project Category field for builders
        if ($isBuilder) {
            $builder->add('ProjectCategory', ChoiceType::class, [
                'label' => 'Project Type: ',
                'placeholder' => 'Select Project Type',
                'choices' => [
                    'Residential' => 'Residential',
                    'Commercial' => 'Commercial',
                    'Industrial' => 'Industrial',
                    'Warehouse' => 'Warehouse',
                    'Plots' => 'Plots'
                ],
                'required' => true,
            ])
            ->add('FloorMaps', FileType::class, [
                'label' => 'Add Floor Maps: ',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'floor-maps-input',
                    'data-preview-container' => 'floor-maps-preview-container',
                ],
                'required' => false,
                'multiple' => 'multiple',
            ]);
        }

        // Price range options differ for builders
        $priceRangeOptions = $isBuilder ? [
            '10-25 Lakhs' => '10-25 Lakhs',
            '25 to 50 Lakhs' => '25 to 50 Lakhs',
            '50 to 1 Crore' => '50 to 1 Crore',
            '1 crore to 3 crore' => '1 crore to 3 crore',
            '3-5 crore' => '3-5 crore',
            'Above 5 crore' => 'Above 5 crore'
        ] : [
            'Less than 1 Lakh' => 'Less than 1 Lakh',
            '1 Lakh to 10 Lakhs' => '1 Lakh to 10 Lakhs',
            'More than 10 Lakhs' => 'More than 10 Lakhs',
        ];

        $builder
            ->add('PriceRange', ChoiceType::class, [
                'label' => 'Project Value: ',
                'placeholder' => 'Select',
                'choices' => $priceRangeOptions,
                'required' => false,
            ]);
            if (!$isBuilder) {
                $builder->add('Configuration', TextType::class, [
                    'label' => 'Project Specification',
                    'attr' => [
                        'maxlength' => 100,
                    ],
                    'required' => true
                ])
                ->add('Collaborations', TextareaType::class, [
                    'label' => 'Collaborations & Credits',
                    'attr' => [
                        'maxlength' => 100
                    ],
                    'required' => false
                ]);
            }

            $builder->add('_submit', SubmitType::class, [
                'label' => 'Publish',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            ]);
    }
}