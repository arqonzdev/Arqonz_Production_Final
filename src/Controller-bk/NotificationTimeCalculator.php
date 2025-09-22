<?php
// src/AppBundle/Calculator/NotificationTimeCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class NotificationTimeCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "Time") {
            $creationTimestamp = $object->getCreationDate();
            
            if ($creationTimestamp) {
                // Convert Unix timestamp to DateTime object
                $publishTime = \DateTime::createFromFormat('U', $creationTimestamp);
                
                // Calculate time difference
                $currentTime = new \DateTime();
                $interval = $currentTime->diff($publishTime);
                
                // Format the result based on the interval
                if ($interval->y > 0) {
                    return $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
                } elseif ($interval->m > 0) {
                    return $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ago";
                } elseif ($interval->d > 0) {
                    return $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
                } elseif ($interval->h > 0) {
                    return $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
                } elseif ($interval->i > 0) {
                    return $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
                } else {
                    return "Just now";
                }
            } else {
                return "Not published";
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