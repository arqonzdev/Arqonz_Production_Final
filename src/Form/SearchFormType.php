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




class SearchFormType extends AbstractType
{

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'choices' => $this->getCitiesChoices(),
    //         // Other options...
    //     ]);
    // }

    // private function getCitiesChoices(): array
    // {
    //     $jsonUrl = 'https://gist.githubusercontent.com/desaurabh/25a46d20c266b86a054b/raw/e613fe161964d7a668e44bc786b3fad776c33061/Indian-States-&-Cities.json';

    //     // Fetch data from the URL
    //     $jsonContent = file_get_contents($jsonUrl);

    //     // Decode JSON content
    //     $decoder = new JsonDecode();
    //     $data = $decoder->decode($jsonContent, 'json');

    //     // Format data for choices
    //     $choices = [];
    //     foreach ($data as $state => $cities) {
    //         $stateChoices = [];
    //         foreach ($cities as $cityData) {
    //             $stateChoices[$cityData->city] = $cityData->city;
    //         }
    //         $choices[$state] = $stateChoices;
    //     }

    //     return $choices;
    // }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getCitiesChoices(),
            // Other options...
        ]);
    }

    private function getCitiesChoices(): array
    {
        $jsonUrl = 'static/files/Indian-States-&-Cities.json';

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
        ->add('Search', TextType::class, [
            'label' => 'Search Professionals',
            'attr' => [
                'maxlength' => 190
            ],
            'required' => true,
            
        ]);

        $builder
            ->add('_submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => [
                    'class' => 'btn btn-lg btn-block btn-success'
                ]
            
            ]);
    }



   
}

