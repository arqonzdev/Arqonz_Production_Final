<?php
// src/AppBundle/Calculator/ProfileCompletionCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class ProfileCompletionCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "ProfileCompletion") {
            // Base profile completion percentage
            $profileCompletion = 22;

            // Check if the portfolio is activated
            $ProProfileActivate = $object->getPortfolioActivate();
            if ($ProProfileActivate == 'true') {
                $profileCompletion = 50;

                // Get the ProProfile object
                $ProProfiles = $object->getPortfolio();
                $ProProfile = isset($ProProfiles[0]) ? $ProProfiles[0] : null;

                if ($ProProfile) {
                    // Check if there's at least one project
                    if (count($ProProfile->getProjects()) > 0) {
                        $profileCompletion += 25; // Add 25% for having at least one project
                    }

                    // Check endorsements and calculate the extra percentage
                    $endorsements = $ProProfile->getEndorsements();
                    $endorsementCount = count($endorsements);
                    if ($endorsementCount > 0) {
                        // Add 5% for each endorsement, up to 5 endorsements (max 25%)
                        $profileCompletion += min(25, $endorsementCount * 5);
                    }
                }
            }

            // Ensure the profile completion percentage doesn't exceed 100%
            return (string) min($profileCompletion, 100);
        } else {
            \Logger::error("Unknown field: " . $context->getFieldname());
            return "Error";
        }
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
