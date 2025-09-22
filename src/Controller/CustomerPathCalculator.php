<?php
// src/AppBundle/Calculator/ProfessionalPathCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class CustomerPathCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "CustomerPath") {
            // Assuming "Professional" is a Relation field
            $CustomerObject = $object->getcustomer();
            
            if ($CustomerObject instanceof Concrete) {
                // Get the path of the related object
                return $CustomerObject->getFullPath();
            } else {
                return "Customer not provided";
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