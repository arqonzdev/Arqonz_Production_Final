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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class ProRequirementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'maxlength' => 190,
                    'minlength' => 10, // minimum characters for a meaningful title
                    'placeholder' => 'Enter a descriptive title',
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Title is required.']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 190,
                        'minMessage' => 'Title must be at least {{ limit }} characters.',
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'maxlength' => 300,
                    'minlength' => 20, // minimum characters for a meaningful description
                    'placeholder' => 'Provide a detailed description',
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description is required.']),
                    new Assert\Length([
                        'min' => 20,
                        'max' => 300,
                        'minMessage' => 'Description must be at least {{ limit }} characters.',
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('City', TextType::class, [
                'label' => 'City',
                'attr' => [
                    'maxlength' => 100,
                    'placeholder' => 'Enter the city name',
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'City is required.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'City name must be at least {{ limit }} characters.',
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'City name should only contain letters and spaces.',
                    ]),
                ],
            ])
            ->add('TargetPrice', IntegerType::class, [
                'label' => 'Target Price (In Rupees)',
                'attr' => [
                    'placeholder' => 'Enter target price in INR',
                ],
                'required' => false,
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'The target price must be a positive number.',
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 10000000,
                        'message' => 'The target price should not exceed 10 million INR.',
                    ]),
                ],
            ])
            ->add('ExpireDate', DateTimeType::class, [
                'label' => 'End Date',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetime-picker',
                    'min' => (new \DateTime('+1 day'))->format('Y-m-d\TH:i'), // Min date is 1 day from today
                    'max' => (new \DateTime('+30 days'))->format('Y-m-d\TH:i'), // Max date is 30 days from today
                ],
                'model_timezone' => 'Asia/Kolkata',
                'view_timezone' => 'Asia/Kolkata',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Expiry Date is required.']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'Expiry Date must be a future date.',
                    ]),
                ],
            ])
            ->add('excelFile', FileType::class, [
                'label' => 'Excel File',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Excel file is required.']),
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Excel file.',
                    ]),
                ],
                'help' => 'Upload Excel File in XLS or XLSX format. Max size: 10MB.',
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            ]);
    }
}
 