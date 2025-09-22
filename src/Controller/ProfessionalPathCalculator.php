<?php
// src/AppBundle/Calculator/ProfessionalPathCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class ProfessionalPathCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "ProfessionalPath") {
            // Assuming "Professional" is a Relation field
            $professionalObject = $object->getProfessional();
            
            if ($professionalObject instanceof Concrete) {
                // Get the path of the related object
                return $professionalObject->getFullPath();
            } else {
                return "Professional not provided";
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