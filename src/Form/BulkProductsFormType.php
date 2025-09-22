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

class BulkProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ManufacturerID', TextType::class, [
                'label' => 'Manufacturer ID',
                'attr' => [
                    'maxlength' => 190,
                    'minlength' => 2, // minimum characters for a meaningful title
                    'placeholder' => 'Enter Manufacturer ID',
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
            ->add('ImageAssetPath', TextType::class, [
                'label' => 'Image Asset Path',
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => 'e.g., /pictures/myPictures/',
                    'class' => 'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Image Asset Path is required.']),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 500,
                        'minMessage' => 'Image Asset Path must be at least {{ limit }} character.',
                        'maxMessage' => 'Image Asset Path cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\/.*\/$/',
                        'message' => 'Image Asset Path must start and end with forward slashes (e.g., /pictures/myPictures/)',
                    ]),
                ],
                'help' => 'Enter the path where images are stored in Pimcore assets (e.g., /pictures/myPictures/)',
            ])
            ->add('ProductsexcelFile', FileType::class, [
                'label' => 'ProductsExcel File',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Excel file is required.']),
                    new File([
                        'maxSize' => '30M',
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
 