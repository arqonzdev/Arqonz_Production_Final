<?php
// src/AppBundle/Calculator/ExperienceCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class ExperienceCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "Experience") {
            $yearEstablished = $object->getYearEstablished();
            $currentYear = date('Y');
            
            if ($yearEstablished) {
                return $currentYear - $yearEstablished;
            } else {
                return "Year not provided";
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
