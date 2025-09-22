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
use Symfony\Component\Form\Extension\Core\Type\DateType;
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




class ProfessionalRegistrationFormType extends AbstractType
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
            ->add('CompanyName', TextType::class, [
                'label' => 'Company Name:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('Profession', ChoiceType::class, [
                'label' => 'My Profession:',
                'choices' => [
                    'Architect' => 'Architect',
                    'Builder' => 'Builder',
                    'Contractor' => 'Contractor',
                    'Interior Designer' => 'Designer', 
                    'Engineer' => 'Engineer',

                ],
                'expanded' => false, // This makes the radio buttons instead of a dropdown
                'multiple' => false, // Allow only one choice
                'required' => true,
                'placeholder' => 'Choose an option',
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Company Description:',
                'attr' => [
                    'maxlength' => 1000,
                    'style' => 'height: 150px;'
                ],
                'required' => true
            ])
            ->add('Specialization', TextType::class, [
                'label' => 'Specialization:',
                'attr' => [
                    'maxlength' => 190,
                    'data-hint' => 'Example: High Raise Blueprinting, Commercial Mall, ExpressWay'
                ],
                'required' => true,
                
            ])
            ->add('gstnumber', TextType::class, [
                'label' => 'GST Number:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => false
            ])
            // ->add('url', TextType::class, [
            //     'label' => 'Portfolio Username:',
            //     'attr' => [
            //         'maxlength' => 50,
            //     ],
            //     'required' => true,
            //     'constraints' => [
            //         new Callback([
            //             'callback' => [$this, 'validateUsernameAvailability'],
            //         ]),
            //     ],
            // ])
            ->add('ProfileImage', FileType::class, [
                'label' => 'Display Picture:',
                'attr' => [
                    'placeholder' => '',
                    'data-hint' => 'Max size: 300KB. Allowed types: png, jpg, jpeg, gif.'
                ],
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '300k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (png, jpg, jpeg, gif)',
                    ]),
                ],
                
            ])
            ->add('YearEstablished', ChoiceType::class, [
                'label' => 'Year Established:',
                'choices' => $this->getYearChoices(),
                'placeholder' => '',
                'required' => true,
            ])
            
            // ->add('YearEstablished', TextType::class, [
            //     'label' => 'Year Established:',
            //     'required' => true,
            //     'attr' => [
            //         'class' => 'flatpickr',
            //         'data-flatpickr' => '{"enableTime": false, "dateFormat": "Y"}',
            //     ],
            // ])
            // ->add('YearEstablished', DateType::class, [
            //     'label' => 'Year Established:',
                
            //     'required' => true
            //     'attr' => [
            //         'maxlength' => 4
            //     ],
                
            // ])
            
            // ->add('YearOfCertification', ChoiceType::class, [
            //     'label' => 'Year Of Certification:',
            //     'choices' => $this->getYearChoices(),
            //     'required' => false
            // ])
            // ->add('CoaNumber', TextType::class, [
            //     'label' => 'COA Number:',
            //     'attr' => [
            //         'maxlength' => 50
            //     ],
            //     'required' => false
            // ])
            ->add('PriceForHour', IntegerType::class, [
                'label' => 'Price For Hour: (₹)',
                'attr' => [
                    'maxlength' => 6,
                ],
                'required' => true
            ])
            ->add('Skills', ChoiceType::class, [
                'label' => 'Select Your Skills:',
                'required' => true,
                'choices' => [
                    'Architecture' => 'Architecture',
                    '3D Design' => '3D Design',
                    'Interior Design' => 'Interior Design',
                    'Autodesk AutoCAD' => 'Autodesk AutoCAD',
                    'Building Regulations' => 'Building Regulations',
                    'Constructions' => 'Constructions',
                    'Structural Specialist' => 'Structural Specialist',
                    'Innovative Designers' => 'Innovative Designers',
                    'Site Supervision' => 'Site Supervision',
                    'Project Management' => 'Project Management',
                    'Cost Estimation' => 'Cost Estimation',
                    'Construction Safety' => 'Construction Safety',
                    'Quality Control' => 'Quality Control',
                    'Materials Management' => 'Materials Management',
                    'Urban Planning' => 'Urban Planning',
                    'Sustainable Design' => 'Sustainable Design',
                    'Landscape Architecture' => 'Landscape Architecture',
                    'Space Planning' => 'Space Planning',
                    'Conceptual Design' => 'Conceptual Design',
                    '3D Rendering' => '3D Rendering',
                    'BIM (Building Information Modeling)' => 'BIM (Building Information Modeling)',
                    'Revit Architecture' => 'Revit Architecture',
                    'SketchUp' => 'SketchUp',
                    'Lumion' => 'Lumion',
                    'Structural Engineering' => 'Structural Engineering',
                    'Civil Engineering' => 'Civil Engineering',
                    'MEP Engineering (Mechanical, Electrical, Plumbing)' => 'MEP Engineering (Mechanical, Electrical, Plumbing)',
                    'Geotechnical Engineering' => 'Geotechnical Engineering',
                    'Environmental Engineering' => 'Environmental Engineering',
                    'Furniture Design' => 'Furniture Design',
                    'Lighting Design' => 'Lighting Design',
                    'Space Optimization' => 'Space Optimization',
                    'Color Theory' => 'Color Theory',
                    'Decor Styling' => 'Decor Styling',
                    'Building Codes' => 'Building Codes',
                    'Environmental Regulations' => 'Environmental Regulations',
                    'Fire Safety Standards' => 'Fire Safety Standards',
                    'Accessibility Standards' => 'Accessibility Standards',
                    'Primavera P6' => 'Primavera P6',
                    'MS Project' => 'MS Project',
                    'SAP2000' => 'SAP2000',
                    'STAAD.Pro' => 'STAAD.Pro',
                    'Carpentry' => 'Carpentry',
                    'Masonry' => 'Masonry',
                    'Plumbing' => 'Plumbing',
                    'Electrical Wiring' => 'Electrical Wiring',
                    'HVAC Installation' => 'HVAC Installation',
                    'LEED Certification' => 'LEED Certification',
                    'Passive Design Principles' => 'Passive Design Principles',
                    'Renewable Energy Systems' => 'Renewable Energy Systems',
                    'Energy Efficiency Audits' => 'Energy Efficiency Audits',
                    'Modular Construction' => 'Modular Construction',
                    'Prefabrication Techniques' => 'Prefabrication Techniques',
                    'Smart Building Systems' => 'Smart Building Systems',
                    'IoT Integration' => 'IoT Integration',
                    'Contract Drafting' => 'Contract Drafting',
                    'Tendering' => 'Tendering',
                    'Vendor Management' => 'Vendor Management',
                    'Legal Compliance' => 'Legal Compliance',
                    'Communication Skills' => 'Communication Skills',
                    'Team Leadership' => 'Team Leadership',
                    'Problem-Solving' => 'Problem-Solving',
                    'Negotiation' => 'Negotiation',
                    

                    // Add more skills as needed
                ],
                'multiple' => true,
                'attr' => [
                    'class' => 'js-select-skills'
                ],
            ])
            ->add('CountryCode', TextType::class, [
                'label' => 'Code',
                'attr' => [
                    'maxlength' => 4,
                    
                    'pattern' => '\+[0-9]+',  // Pattern to allow only digits after the '+'
                    'placeholder' => '+91',    // Placeholder to show the '+' sign
                ],
                'required' => false,
                'data' => '+91'
            ])
            ->add('PhoneNumber', IntegerType::class, [
                'label' => 'Phone Number:',
                'attr' => [
                    'maxlength' => 10
                ],
                'required' => true
            ])
            ->add('StreetAddress', TextType::class, [
                'label' => 'Street Address:',
                'attr' => [
                    'maxlength' => 50
                ],
                'required' => true
            ])
            ->add('City', ChoiceType::class, [
                'label' => 'City:',
                'choices' => $options['choices'],
                'placeholder' => 'Select City',
                'multiple' => false,
                'choice_value' => function ($value) {
                    return $value;
                },
            ])
            ->add('CitiesServed', ChoiceType::class, [
                'label' => 'Cities Served',
                'choices' => $options['choices'],
                'multiple' => true,
                'choice_value' => function ($value) {
                    return $value;
                },
            ])
            ->add('State', ChoiceType::class, [
                'label' => 'State:',
                'choices' => $options['states_choices'],
                'placeholder' => 'Select State',
                'required' => true,
            ])
            ->add('Country', ChoiceType::class, [
                'label' => 'Country:',
                'choices' => ['India' => 'India'], 
                'required' => true,
            ])
            ->add('PinCode', TextType::class, [
                'label' => 'Pin Code:',
                'attr' => [
                    'maxlength' => 50
                ],
                'required' => true
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

