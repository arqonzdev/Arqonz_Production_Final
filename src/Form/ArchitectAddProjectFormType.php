<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class ArchitectAddProjectFormType extends AbstractType
{   
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getCitiesChoices(),
            // Other options...
        ]);
    }

    private function getCitiesChoices(): array
    {
        $jsonUrl = 'https://gist.githubusercontent.com/desaurabh/25a46d20c266b86a054b/raw/e613fe161964d7a668e44bc786b3fad776c33061/Indian-States-&-Cities.json';

        // Fetch data from the URL
        $jsonContent = file_get_contents($jsonUrl);

        // Decode JSON content
        $decoder = new JsonDecode();
        $data = $decoder->decode($jsonContent, 'json');

        // Format data for choices
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


    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ProjectName', TextType::class, [
                'label' => 'Project Title',
                'attr' => [
                    'placeholder' => 'Enter Title',
                    'maxlength' => 190
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Project Title is required.']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 190,
                        'minMessage' => 'Project Title must be at least {{ limit }} characters.',
                        'maxMessage' => 'Project Title cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('ProjectDescription', TextareaType::class, [
                'label' => 'Project Description',
                'attr' => [
                    'placeholder' => 'Enter Description',
                    'maxlength' => 1000,
                    'style' => 'height: 150px;'
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Project Description is required.']),
                    new Assert\Length([
                        'min' => 20,
                        'max' => 1000,
                        'minMessage' => 'Project Description must be at least {{ limit }} characters.',
                        'maxMessage' => 'Project Description cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('ProjectGallery', FileType::class, [
                'label' => 'Project Gallery',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'image-input',
                    'data-preview-container' => 'image-preview-container',
                ],
                'required' => false,
                'multiple' => 'multiple',

            ])
            // ->add('ProjectGallery', FileType::class, [
            //     'label' => 'Project Gallery',
            //     'required' => false,
            //     'multiple' => true,
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '5M',
            //             'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
            //             'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
            //             'maxSizeMessage' => 'Each image should not exceed 5MB.',
            //         ]),
            //     ],
            //     'help' => 'Upload images in JPG, PNG, or GIF format. Max size: 5MB per file.',
            // ])
            ->add('Location', ChoiceType::class, [
                'label' => 'Project Location',
                'choices' => $options['choices'],
                'placeholder' => 'Select City',
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Project location is required.']),
                ],
            ])
            ->add('PriceRange', ChoiceType::class, [
                'label' => 'Project Value',
                'placeholder' => 'Select',
                'choices' => [
                    'Less than 1 Lakh' => 'Less than 1 Lakh',
                    '1 Lakh to 10 Lakhs' => '1 Lakh to 10 Lakhs',
                    'More than 10 Lakhs' => 'More than 10 Lakhs',
                ],
                'required' => false,
            ])
            ->add('Configuration', TextType::class, [
                'label' => 'Project Specification',
                'attr' => [
                    'placeholder' => 'Enter Configuration of Project',
                    'maxlength' => 100,
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Project Specification is required.']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Project Specification must be at least {{ limit }} characters.',
                        'maxMessage' => 'Project Specification cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('Collaborations', TextareaType::class, [
                'label' => 'Collaborations & Credits',
                'attr' => [
                    'placeholder' => 'Enter Email Address (,) Separated',
                    'maxlength' => 500
                ],
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'Collaborations & Credits cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,},?\s*)*$/',
                        'message' => 'Enter valid email addresses separated by commas.',
                    ]),
                ],
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Publish',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            ]);
    }

}
