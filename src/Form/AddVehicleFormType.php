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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Pimcore\Model\DataObject\ArchitectProfile;
use Pimcore\Model\Listing;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\File;




class AddVehicleFormType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getCitiesChoices(),
            'states_choices' => $this->getStatesChoices(),
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

    private function getStatesChoices(): array
    {
        $jsonUrl = 'https://gist.githubusercontent.com/desaurabh/25a46d20c266b86a054b/raw/e613fe161964d7a668e44bc786b3fad776c33061/Indian-States-&-Cities.json';

        // Fetch data from the URL
        $jsonContent = file_get_contents($jsonUrl);

        // Decode JSON content as an associative array
        $decoder = new JsonDecode();
        $data = $decoder->decode($jsonContent, 'json');

        // Extract unique state names
        $stateChoices = array_keys((array) $data);

        // Format data for choices
        $choices = [];
        foreach ($stateChoices as $state) {
            $choices[$state] = $state;
        }

        return $choices;
    }

    private function getYearChoices(): array
    {
        // Generate an array of years, you can customize the range as needed
        $currentYear = (int) date('Y');
        $years = range($currentYear - 100, $currentYear);

        // Reverse the array to have the most recent year first
        $reversedYears = array_reverse($years);

        // Combine the reversed array to have years as both keys and values
        return array_combine($reversedYears, $reversedYears);
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('VehicleBrand', TextType::class, [
                'label' => 'Brand (Vehicle Make):',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('VehicleType', ChoiceType::class, [
                'label' => 'Contractor Type:',
                'choices' => [
                    'Excavators' => 'Excavators',
                    'Bulldozers' => 'Bulldozers',
                    'Crawler Loaders' => 'Crawler Loaders',
                    'Wheel Tractor-Scrapers' => 'Wheel Tractor-Scrapers',
                    'Dump Trucks' => 'Dump Trucks',
                    'Pile Drivers' => 'Pile Drivers',
                    'Loaders' => 'Loaders',
                    'Telehandlers' => 'Telehandlers',
                    'Backhoe Loaders' => 'Backhoe Loaders',
                    'Skid Steer Loaders' => 'Skid Steer Loaders',
                    'Motor Graders' => 'Motor Graders',
                    'Trenchers' => 'Trenchers',
                    'Drilling Rigs' => 'Drilling Rigs',
                    'Compactors' => 'Compactors',
                    'Scrapers' => 'Scrapers',

                    // Add other types as needed
                ],
                'placeholder' => 'Select Vehicle Type',
                'required' => true
            ])
            ->add('VehicleModel', TextType::class, [
                'label' => 'Vehicle Model:',
                'attr' => [
                    'maxlength' => 200
                ],
                'required' => true
            ])
            ->add('VehicleNumber', TextType::class, [
                'label' => 'Vehicle No.:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('YearManufactured', IntegerType::class, [
                'label' => 'Year Manufactured:',
                'attr' => [
                    'maxlength' => 10
                ],
            ])
            ->add('EngineType', ChoiceType::class, [
                'label' => 'Engine Type:',
                'choices' => [
                    'Diesel Engine' => 'Diesel Engine',
                    'Petrol Engine' => 'Petrol Engine',
                    'Electric Motor' => 'Electric Motor',
                    'CNG Engine' => 'Compressed Natural Gas (CNG) Engine',
                    'Wheel Tractor-Scrapers' => 'Wheel Tractor-Scrapers',

                    // Add other types as needed
                ],
                'placeholder' => 'Select Engine Type',
                'required' => true
            ])
            ->add('Capacity', TextType::class, [
                'label' => 'Vehicle Capacity:',
                'attr' => [
                    'maxlength' => 100,
                ],
                'required' => true
            ])
            ->add('AvailabilityStatus', ChoiceType::class, [
                'label' => 'Availability Status:',
                'choices' => [
                    'Available' => 'Available',
                    'UnAvailable' => 'UnAvailable',

                    // Add other types as needed
                ],
                'placeholder' => 'Select Vehicle Availability',
                'required' => true
            ])
            ->add('InsuranceStatus', ChoiceType::class, [
                'label' => 'Insurance Status:',
                'choices' => [
                    'Active' => 'Active',
                    'InActive' => 'InActive',

                    // Add other types as needed
                ],
                'placeholder' => 'Select Insurance Status',
                'required' => true
            ])
            ->add('VehicleGallery', FileType::class, [
                'label' => 'Vehicle Gallery',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'image-input',
                    'data-preview-container' => 'image-preview-container',
                ],
                'required' => false,
                'multiple' => 'multiple',

            ])
            ->add('OperatorProvided', ChoiceType::class, [
                'label' => 'Operator Provided:',
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No',

                    // Add other types as needed
                ],
                'placeholder' => 'Select',
                'required' => true
            ])
            ->add('PriceForHour', IntegerType::class, [
                'label' => 'Price For Hour: (₹)',
                'attr' => [
                    'maxlength' => 6,
                ],
                'required' => true
            ])

            ->add('CitiesServed', ChoiceType::class, [
                'label' => 'Cities Served',
                'choices' => $options['choices'],
                'multiple' => true,
                'choice_value' => function ($value) {
                    return $value;
                },
            ])
            
            ->add('UsageRestrictions', TextareaType::class, [
                'label' => 'Usage Restrictions (Optional):',
                'attr' => [
                    'maxlength' => 500
                ],
            ]);

        $builder
            ->add('_submit', SubmitType::class, [
                'label' => 'Register',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            ]);
    }

    public function validateUsernameAvailability($value, ExecutionContextInterface $context)
    {
        // Check if there is an existing ArchitectProfile with the same URL
        
        $existingProfileListing = ArchitectProfile::getList([
            'url' => $value,
            'unpublished' => false,
        ]);
        
        $existingProfile = $existingProfileListing->getObjects();
        $matchingProfile = null;

        foreach ($existingProfileListing as $profile) {
            if ($profile->geturl() === $value) {
                $matchingProfile = $profile;
                break; // Exit the loop once a match is found
            }
        }

        // echo '<pre>';
        // var_dump($value);
        // var_dump($matchingProfile);
        // echo '</pre>';

        // If an existing profile is found, add a violation
        if ($matchingProfile) {
            $context->buildViolation('The provided URL is not available!')
                ->atPath('url')
                ->addViolation();

        }
    }


   
}

