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
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class MobileRegistrationFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customertype', ChoiceType::class, [
                'label' => 'My Profession:',
                'choices' => [
                    'Professional' => 'Professional',
                    'Supplier' => 'Supplier',
                ],
                'expanded' => false, // This makes the radio buttons instead of a dropdown
                'multiple' => false, // Allow only one choice
                'required' => true,
                'placeholder' => 'Choose an option',
            ])
            // ->add('customertype', ChoiceType::class, [
            //     'label' => 'My Profession:',
            //     'choices' => [
            //         'Customer' => 'Customer',
            //         'Architect' => 'Architect',
            //         'Builder' => 'Builder',
            //         'Contractor' => 'Contractor',
            //         'Designer' => 'Designer', 
            //         'Manufacturer' => 'Manufacturer',
            //         'Dealer' => 'Dealer',
            //         'Distributor' => 'Distributor',
            //         'Engineer' => 'Engineer',
            //         'Retailer' => 'Retailer',
                    
                  
            //     ],
            //     'expanded' => false, // This makes the radio buttons instead of a dropdown
            //     'multiple' => false, // Allow only one choice
            //     'required' => true,
            //     'placeholder' => 'Choose an option',
            // ])
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('phone', TextType::class, [
                
                'label' => 'Phone:',
                'attr' => [
                    'title' => 'Enter WhatsApp Enabled Phone Number',
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'First Name:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last Name:',
                'attr' => [
                    'maxlength' => 190
                ],
                'required' => true
            ])
            // ->add('AgentID', TextType::class, [
            //     'label' => 'Agent ID',
            //     'attr' => [
            //         'maxlength' => 190
            //     ],
            //     'required' => true
            // ])
            ->add('AgentID', ChoiceType::class, [
                'label' => 'Agent ID',
                'choices' => ['Select an Agent' => ''] + $options['Agents'], // Add empty option at the beginning
                'required' => false,
                'placeholder' => 'Select an Agent', // This will act as a placeholder
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false, // This field is not mapped to the Customer entity
            ]);
        if (!$options['hidePassword']) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Password:',
                'attr' => [
                    'maxlength' => PasswordHasherInterface::MAX_PASSWORD_LENGTH
                ]
            ]);
        }

        // $builder
        //     ->add('newsletter', CheckboxType::class, [
        //         'label' => 'general.newsletter',
        //         'required' => false,
        //         'label_attr' => [
        //             'class' => 'checkbox-custom'
        //         ]
        //     ])
        //     ->add('profiling', CheckboxType::class, [
        //         'label' => 'general.profiling',
        //         'required' => false,
        //         'label_attr' => [
        //             'class' => 'checkbox-custom'
        //         ]
        //     ]);

        // $builder
        //     ->add('oAuthKey', HiddenType::class)
        //     ->add('_submit', SubmitType::class, [
        //         'label' => 'Register',
        //         'attr' => [
        //             'class' => 'btn btn-lg btn-block btn-success'
        //         ]
        //     ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'hidePassword' => false,
            'csrf_protection' => false,
            'Agents' => [], // Default empty array for channel partners
        ]);
    }
}
