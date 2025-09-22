<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Carbon\Carbon;

class SubscriptionCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() !== "SubscriptionStatus") {
            return "Invalid field";
        }

        $plan = $object->getSubscriptionPlan();
        $startDate = $object->getSubscriptionStart();
        $currentDate = Carbon::now();

        if (!$plan || !$startDate) {
            return "No active subscription";
        }

        // Determine subscription duration
        $durationMonths = match ($plan) {
            'Standard' => 1,
            'Silver' => 3,
            'Gold' => 6,
            'Platinum' => 12,
            default => 0,
        };

        if ($durationMonths === 0) {
            return "Invalid subscription plan";
        }

        $expiryDate = Carbon::parse($startDate)->addMonths($durationMonths);

        if ($currentDate->greaterThanOrEqualTo($expiryDate)) {
            // Subscription expired
            $object->setCreditPoints(0);
            $object->setSubscriptionPlan('None');
            $object->setSubscriptionStart(null);
            $object->save();
            return "Subscription expired and credits cleared";
        }

        return "Active until: " . $expiryDate->format('Y-m-d');
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
