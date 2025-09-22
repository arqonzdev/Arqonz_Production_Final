<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlobalAwardsFormType extends AbstractType
{   
    
    
    private function getCountryChoices(): array
    {
        $jsonUrl = 'https://gist.githubusercontent.com/almost/7748738/raw/575f851d945e2a9e6859fb2308e95a3697bea115/countries.json';

        // Fetch data from the URL
        $jsonContent = @file_get_contents($jsonUrl);
        if ($jsonContent === false) {
            throw new \RuntimeException("Unable to fetch country data from the URL.");
        }

        // Decode JSON content
        $data = json_decode($jsonContent, true);

        // Check for JSON errors
        if ($data === null) {
            $error = json_last_error_msg(); // Get detailed error message
            throw new \RuntimeException("Invalid JSON format in country data: $error");
        }

        // Format data for choices
        $choices = [];
        foreach ($data as $country) {
            if (isset($country['code']) && isset($country['name'])) {
                // Use country code as key, but display the country name in the dropdown
                $choices[$country['name']] = $country['code'];
            }
        }

        return $choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('OrganizationName', TextType::class, [
                'label' => 'Name of Organization *',
                'required' => true,
                'attr' => [],
            ])
            ->add('OfficeAddress', TextareaType::class, [
                'label' => 'Office Address *',
                'required' => true,
                'attr' => [],
            ])
            ->add('stateProvince', TextareaType::class, [
                'label' => 'State/Province *',
                'required' => true,
                'attr' => [],
            ])
            ->add('Country', ChoiceType::class, [
                'label' => 'Country *',
                'choices' => $this->getCountryChoices(),
                'placeholder' => 'Select Country',
                'data' => 'IN', // Set default value to India
                'required' => true,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Zip Code *',
                'required' => true,
                'attr' => [],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email ID *',
                'required' => true,
                'attr' => [],
            ])
            ->add('whatsappNumber', TextType::class, [
                'label' => 'Whatsapp Number for communication *',
                'required' => true,
                'attr' => [],
            ])
            ->add('projectType', TextType::class, [
                'label' => 'Project Type',
                'required' => false,
                'attr' => [],
            ])
            ->add('registrationYear', TextType::class, [
                'label' => 'Year of Company Registration *',
                'required' => true,
                'attr' => [],
            ])
            ->add('projectsDone', TextType::class, [
                'label' => 'Number of Projects Done till Date *',
                'required' => true,
                'attr' => [],
            ])
            ->add('applicantName', TextType::class, [
                'label' => 'Name of Applicant *',
                'required' => true,
                'attr' => [],
            ])
            ->add('designation', TextType::class, [
                'label' => 'Applicant’s Designation *',
                'required' => true,
                'attr' => [],
            ])
            ->add('age', TextType::class, [
                'label' => 'Applicant’s Age *',
                'required' => true,
                'attr' => [],
            ])
            ->add('experience', TextType::class, [
                'label' => 'Applicant’s Experience in the Relevant Field (Years) *',
                'required' => true,
                'attr' => [],
            ])
            ->add('contactNumber', TextType::class, [
                'label' => 'Contact Number of Applicant *',
                'required' => true,
                'attr' => [],
            ])
            ->add('achievements', TextareaType::class, [
                'label' => 'Any Recent Achievements/Accolades',
                'required' => false,
                'attr' => [],
            ])
            ->add('nominatingProject', TextType::class, [
                'label' => 'Name of Nominating Project *',
                'required' => true,
                'attr' => [],
            ])
            ->add('projectLocation', TextareaType::class, [
                'label' => 'Full Location of Project *',
                'required' => true,
                'attr' => [],
            ])
            ->add('ProjectCountry', ChoiceType::class, [
                'label' => 'Project Country *',
                'choices' => $this->getCountryChoices(),
                'placeholder' => 'Select Country',
                'data' => 'IN', // Set default value to India
                'required' => true,
            ])
            ->add('projectCategory', ChoiceType::class, [
                'label' => 'Project Category',
                'required' => true, // or false, based on your needs
                'placeholder' => 'Project Category', // Default placeholder
                'choices' => [
                    'Architecture' => [
                        'Best Architect for Residential Project' => 'Best Architect for Residential Project',
                        'Best Architect for Commercial Project' => 'Best Architect for Commercial Project',
                        'Best Architect for Luxury Project' => 'Best Architect for Luxury Project',
                        'Best Architect for High Rise Infra' => 'Best Architect for High Rise Infra',
                        'Best Architectural Firm of the Year' => 'Best Architectural Firm of the Year',
                        'Most Promising Architect of the Year' => 'Most Promising Architect of the Year',
                        'Best Architect for Private Housing Project' => 'Best Architect for Private Housing Project',
                        'Best Architect for Independent Villa/Bungalow' => 'Best Architect for Independent Villa/Bungalow',
                        'Most Creative Architect of the Year' => 'Most Creative Architect of the Year',
                        'Most Innovative Architect of the Year' => 'Most Innovative Architect of the Year',
                        'Best Urban Design Architect of the Year' => 'Best Urban Design Architect of the Year',
                        'Best Upcoming Architecture of the Year' => 'Best Upcoming Architecture of the Year',
                        'Youngest Most Talented Architect of the Year' => 'Youngest Most Talented Architect of the Year',
                        'Emerging Architect of the Year' => 'Emerging Architect of the Year',
                        'Young Achievers Award' => 'Young Achievers Award',
                        'Women Achievers Award' => 'Women Achievers Award',
                        'Lifetime Achievement Award in Architecture' => 'Lifetime Achievement Award in Architecture',
                        'Best Designer for Landscaping Project of the Year' => 'Best Designer for Landscaping Project of the Year',
                        'Most Creative Architectural Project of the Year' => 'Most Creative Architectural Project of the Year',
                        'Best Artist for 3D Elevation' => 'Best Artist for 3D Elevation',
                    ],
                    'Project & Developers' => [
                        'Developer of the Year – Residential' => 'Developer of the Year – Residential',
                        'Developer of the Year – Commercial' => 'Developer of the Year – Commercial',
                        'Best Luxury Project of the Year' => 'Best Luxury Project of the Year',
                        'Best Luxury Residential Project of the Year' => 'Best Luxury Residential Project of the Year',
                        'Best Luxury Commercial Project of the Year' => 'Best Luxury Commercial Project of the Year',
                        'Best Affordable Residential Project of the Year' => 'Best Affordable Residential Project of the Year',
                        'Best Affordable Commercial Project of the Year' => 'Best Affordable Commercial Project of the Year',
                        'Most Eco-Friendly Project of the Year' => 'Most Eco-Friendly Project of the Year',
                        'Most Creative Project of the Year' => 'Most Creative Project of the Year',
                        'Most Innovative Project of the Year' => 'Most Innovative Project of the Year',
                        'Most Sustainable Project of the Year' => 'Most Sustainable Project of the Year',
                        'Best Housing Project of the Year' => 'Best Housing Project of the Year',
                        'Best Bungalow Project of the Year' => 'Best Bungalow Project of the Year',
                        'Affordable Housing Project of the Year' => 'Affordable Housing Project of the Year',
                        'Modern Housing Project of the Year' => 'Modern Housing Project of the Year',
                        'Best Creative Housing Project of the Year' => 'Best Creative Housing Project of the Year',
                        'Best Villa Project of the Year' => 'Best Villa Project of the Year',
                        'Best Hospitality Project of the Year' => 'Best Hospitality Project of the Year',
                        'Best Architectural Project of the Year' => 'Best Architectural Project of the Year',
                        'Best Interior based Project of the Year' => 'Best Interior based Project of the Year',
                        'Best Industrial Project of the Year' => 'Best Industrial Project of the Year',
                        'Best Independent Housing Project of the Year' => 'Best Independent Housing Project of the Year',
                        'Luxury Modern Housing Project of the Year' => 'Luxury Modern Housing Project of the Year',
                        'Best Project with Green Architectural Design' => 'Best Project with Green Architectural Design',
                        'Best Green Building Solution Project of the Year' => 'Best Green Building Solution Project of the Year',
                        'Best Heritage/ Cultural/ Religious Project of the Year' => 'Best Heritage/ Cultural/ Religious Project of the Year',
                        'Most Creative Design of the Year' => 'Most Creative Design of the Year',
                        'Best Aesthetic Project of the Year' => 'Best Aesthetic Project of the Year',
                        'Best Urban Design Project of the Year' => 'Best Urban Design Project of the Year',
                        'Best Contemporary Project of the Year' => 'Best Contemporary Project of the Year',
                        'Best Recreational/ Renovation/ Refurbished Project of the Year' => 'Best Recreational/ Renovation/ Refurbished Project of the Year',
                        'Modern Architectural Project of the Year' => 'Modern Architectural Project of the Year',
                        'Best Landscape Project of the Year' => 'Best Landscape Project of the Year',
                        'Best Township Project of the Year' => 'Best Township Project of the Year',
                        'Best Mall of the Year' => 'Best Mall of the Year',
                        'Best Beach Resort of the Year' => 'Best Beach Resort of the Year',
                        'Best Service Hotel of the Year' => 'Best Service Hotel of the Year',
                        'Best Airport Hotel of the Year' => 'Best Airport Hotel of the Year',
                        'Most Awaited Residential Project of the Year' => 'Most Awaited Residential Project of the Year',
                        'Most Awaited Commercial Project of the Year' => 'Most Awaited Commercial Project of the Year',
                        'Best Budget Housing Project of the Year' => 'Best Budget Housing Project of the Year',
                        'Smart Project of the Year' => 'Smart Project of the Year',
                        'Themed Project of the Year' => 'Themed Project of the Year',
                        'Real Estate Most Enterprising CEO' => 'Real Estate Most Enterprising CEO',
                        'Best Digital Platform for Real Estate' => 'Best Digital Platform for Real Estate',
                        'Best Religious Project of the Year' => 'Best Religious Project of the Year',
                        'Best Classical Theme based Project of the Year' => 'Best Classical Theme based Project of the Year',
                        'Most Affordable Commercial Project of the Year' => 'Most Affordable Commercial Project of the Year',
                        'Best Developer of the Year' => 'Best Developer of the Year',
                        'Best Planned Resort Project of the Year' => 'Best Planned Resort Project of the Year',
                        'Best Themed Project of the Year' => 'Best Themed Project of the Year',
                        'Best Real Estate Firm for Premium Residences' => 'Best Real Estate Firm for Premium Residences',
                        'Best Healthcare Project of the Year' => 'Best Healthcare Project of the Year',
                        'Best Construction Company of the year' => 'Best Construction Company of the year',
                    ],
                    'Consultants' => [
                        'Best Real Estate Consultant firm' => 'Best Real Estate Consultant firm',
                        'Best Real Estate Project Management Consultancy' => 'Best Real Estate Project Management Consultancy',
                        'Best Independent Real Estate Consultant of the Year' => 'Best Independent Real Estate Consultant of the Year',
                        'Best Upcoming Architecture of the Year' => 'Best Upcoming Architecture of the Year',
                        'Best MEP consultants of the Year' => 'Best MEP consultants of the Year',
                        'Best AMC consultant Service company' => 'Best AMC consultant Service company',
                        'Best Project Management consultant Firm' => 'Best Project Management consultant Firm',
                        'Best Vastu-Consultant of the Year' => 'Best Vastu-Consultant of the Year',
                    ],
                    'Real Estate Service Providers' => [
                        'Most Trusted Brands of Real Estate Industry' => 'Most Trusted Brands of Real Estate Industry',
                        'Cement' => 'Cement',
                        'Steel' => 'Steel',
                        'Bricks & Blocks' => 'Bricks & Blocks',
                        'Paint' => 'Paint',
                        'Electrical Appliance' => 'Electrical Appliance',
                        'Furniture' => 'Furniture',
                        'Glass' => 'Glass',
                        'Tiles' => 'Tiles',
                        'Sanitary Bath Fittings' => 'Sanitary Bath Fittings',
                        'Bath & Kitchen Fittings' => 'Bath & Kitchen Fittings',
                        'Solar Systems' => 'Solar Systems',
                        'Reinforcement Bars' => 'Reinforcement Bars',
                        'Security Systems' => 'Security Systems',
                        'CCTV Camera\'s' => 'CCTV Camera\'s',
                        'Smart Locks Systems' => 'Smart Locks Systems',
                        'Fire & Safety' => 'Fire & Safety',
                        'Brand of the Year' => 'Brand of the Year',
                        'Lifts & Escalators' => 'Lifts & Escalators',
                        'Home Decor & Furnishing' => 'Home Decor & Furnishing',
                    ],
                    'Interior Designes' => [
                        'Best Interior Designer for Residential Project' => 'Best Interior Designer for Residential Project',
                        'Best Interior Designer for Commercial Project' => 'Best Interior Designer for Commercial Project',
                        'Best Interior Designer for Luxury Project' => 'Best Interior Designer for Luxury Project',
                        'Youngest Most Talented Interior Designer of the Year' => 'Youngest Most Talented Interior Designer of the Year',
                        'Best Interior Designer for High Rise Infra' => 'Best Interior Designer for High Rise Infra',
                        'Best Interior Designer of the Year' => 'Best Interior Designer of the Year',
                        'Most Promises Interior Designer of the Year' => 'Most Promises Interior Designer of the Year',
                        'Best Interior Designer for Private Housing Project' => 'Best Interior Designer for Private Housing Project',
                        'Best Interior Designer for Independent Villa/ Bungalow' => 'Best Interior Designer for Independent Villa/ Bungalow',
                        'Most Creative Interior Designer of the Year' => 'Most Creative Interior Designer of the Year',
                        'Most Innovative Interior Designer of the Year' => 'Most Innovative Interior Designer of the Year',
                        'Best Upcoming Interior Designer of the Year' => 'Best Upcoming Interior Designer of the Year',
                        'Best Urban Design Interior Designer of the Year' => 'Best Urban Design Interior Designer of the Year',
                        'Young Achievers Award (Interior Design)' => 'Young Achievers Award (Interior Design)',
                        'Women Achievers Award (Interior Design)' => 'Women Achievers Award (Interior Design)',
                        'Youngest Most Talented Interior Designer of the Year' => 'Youngest Most Talented Interior Designer of the Year',
                        'Emerging Interior Designer of the Year' => 'Emerging Interior Designer of the Year',
                        'Best Interior Designer for Affordable Residential Project of the Year' => 'Best Interior Designer for Affordable Residential Project of the Year',
                        'Best Interior Designing Firm for Classical Project' => 'Best Interior Designing Firm for Classical Project',
                        'Best Interior-based Project of the Year' => 'Best Interior-based Project of the Year',
                    ],

                ],
                'attr' => [
                    'style' => 'width:100%',
                ],
            ])
            ->add('projectStatus', ChoiceType::class, [
                'label' => 'Project Status',
                'required' => false,
                'choices' => [
                    'Designing Phase' => 'Designing Phase',
                    'Non-Completed' => 'Non-Completed',
                    'Completed' => 'Completed',
                ],
                'placeholder' => 'Select a status', // Optional: Adds a placeholder
                'attr' => [],
            ])
            ->add('projectArea', TextType::class, [
                'label' => 'Total Area of Project (Sq.m)',
                'required' => false,
                'attr' => [],
            ])
            ->add('website', TextType::class, [
                'label' => 'Project/Company Website (If any)',
                'required' => false,
                'attr' => [],
            ])
            ->add('keyFeatures', TextareaType::class, [
                'label' => 'Key Features of the Project *',
                'required' => true,
                'attr' => [],
            ])
            ->add('walkthroughLink', TextType::class, [
                'label' => '3D Layout Video/Walkthrough Link (If Available)',
                'required' => false,
                'attr' => [],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-success submitshape-img',
                ],
            ]);
    }

    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
