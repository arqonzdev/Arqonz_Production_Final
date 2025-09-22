<?php
// src/AppBundle/Calculator/ExperienceCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class ProfileCompletionCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "ProfileCompletion") {
            $ProProfileActivate = $object->getPortfolioActivate();
            if ($ProProfileActivate == 'true'){
                return '89';
            }
        else{
            return '22';
        }
        } else {
            \Logger::error("Unknown field");
            return "Error";
        }
    
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
