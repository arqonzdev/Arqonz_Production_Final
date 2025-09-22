<?php
// src/AppBundle/Calculator/NotificationCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\ProProject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ProjectsCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {  
        
        $ProProfile = $object;
        if ($ProProfile) {
            $Projects = new \Pimcore\Model\DataObject\ProProject\Listing();
            $Projects->addConditionParam("ProfessionalPath = ?", $ProProfile);
            
        } else {
            return "0"; // No profile, so no notifications
        }
        
        if ($context->getFieldname() == "ProjectsNumber") {
            // Count the number of items in $NotificationList
            $Projectscount = count($Projects);
            
            // Convert the count to a string and return
            return (string)$Projectscount;
        }

        return "0"; // Default return if fieldname is not "unreadNotifications"
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}