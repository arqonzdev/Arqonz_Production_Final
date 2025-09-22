<?php
// src/AppBundle/Calculator/ExpiryChecker.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\ProRequirement;
use Carbon\Carbon;

class RequirementExpireChecker implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "ExpiryCheck") {
            if ($object instanceof ProRequirement) {
                $expireDate = $object->getExpireDate();

                if ($expireDate instanceof Carbon && $expireDate->isPast()) {
                    // Unpublish the ProRequirement object
                    // $object->setPublished(false);
                    // $object->setStatus("Expired");
                    // $object->save();

                    // Unpublish all related ProRequirementProduct objects
                    // $products = $object->getProRequirementProduct();
                    // foreach ($products as $product) {
                    //     $product->setPublished(false);
                    //     $product->save();
                    // }

                    return "Expired";
                } else {
                    // $object->setStatus("Active");
                    return "Active";
                }
            } else {
                \Logger::error("Invalid object type");
                return "Error";
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
