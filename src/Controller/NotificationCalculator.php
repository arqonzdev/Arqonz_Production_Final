<?php
// src/AppBundle/Calculator/NotificationCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\ProNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class NotificationCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {  
        
        $ProProfile = $object->getPortfolio();
        if ($ProProfile) {
            $NotificationList = new \Pimcore\Model\DataObject\ProNotification\Listing();
            $NotificationList->addConditionParam("professional = ?", $ProProfile);
            $NotificationList->addConditionParam("ReadStatus = ?", 'unread');
        } else {
            return "0"; // No profile, so no notifications
        }
        
        if ($context->getFieldname() == "unreadNotifications") {
            // Count the number of items in $NotificationList
            $unreadNotificationCount = count($NotificationList);
            
            // Convert the count to a string and return
            return (string)$unreadNotificationCount;
        }

        return "0"; // Default return if fieldname is not "unreadNotifications"
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}