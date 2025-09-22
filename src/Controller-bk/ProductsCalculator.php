<?php
// src/AppBundle/Calculator/NotificationCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\ProProduct;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ProductsCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {  
        
        $ProProfile = $object;
        if ($ProProfile) {
            $Products = new \Pimcore\Model\DataObject\ProProduct\Listing();
            $Products->addConditionParam("ProfessionalPath = ?", $ProProfile);
            
        } else {
            return "0"; // No profile, so no notifications
        }
        
        if ($context->getFieldname() == "ProductsNumber") {
            // Count the number of items in $NotificationList
            $Productscount = count($Products);
            
            // Convert the count to a string and return
            return (string)$Productscount;
        }

        return "0"; // Default return if fieldname is not "unreadNotifications"
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}