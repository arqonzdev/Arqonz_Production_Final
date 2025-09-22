<?php
// src/Controller/RatingCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Logger;

class RatingCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "CalculatedRating") {
            try {
                // Calculate average endorsement rating
                $endorsementRating = $this->calculateAverageEndorsementRating($object);
                
                // Calculate average customer review rating
                $customerRating = $this->calculateAverageCustomerRating($object);
                
                // Calculate final rating (average of both if both exist)
                if ($endorsementRating !== null && $customerRating !== null) {
                    $finalRating = ($endorsementRating + $customerRating) / 2;
                } elseif ($endorsementRating !== null) {
                    $finalRating = $endorsementRating;
                } elseif ($customerRating !== null) {
                    $finalRating = $customerRating;
                } else {
                    $finalRating = 0; // Default value if no ratings exist
                }
                
                return (string) round($finalRating, 2); // Round to 2 decimal places
                
            } catch (\Exception $e) {
                Logger::error("Error calculating rating: " . $e->getMessage());
                return "0"; // Return 0 if there's an error
            }
        } else {
            Logger::error("Unknown field: " . $context->getFieldname());
            return "0";
        }
    }

    private function calculateAverageEndorsementRating(Concrete $object): ?float
    {
        try {
            $endorsementsCustomer = $object->getCustomer();
            
            // Check if customer exists and has endorsements
            if (!$endorsementsCustomer || !method_exists($endorsementsCustomer, 'getEndorsement')) {
                return null;
            }
            
            $endorsements = $endorsementsCustomer->getEndorsement();
            if (empty($endorsements)) {
                return null;
            }
            
            $total = 0;
            $count = 0;
            
            foreach ($endorsements as $endorsement) {
                if (!method_exists($endorsement, 'getQ3')) {
                    continue;
                }
                
                $rating = $endorsement->getQ3();
                if (is_numeric($rating)) {
                    $total += (float) $rating;
                    $count++;
                }
            }
            
            return $count > 0 ? $total / $count : null;
            
        } catch (\Exception $e) {
            Logger::error("Error calculating endorsement rating: " . $e->getMessage());
            return null;
        }
    }

    private function calculateAverageCustomerRating(Concrete $object): ?float
    {
        try {
            if (!method_exists($object, 'getProRatings')) {
                return null;
            }
            
            $reviews = $object->getProRatings();
            if (empty($reviews)) {
                return null;
            }
            
            $total = 0;
            $count = 0;
            
            foreach ($reviews as $review) {
                if (!method_exists($review, 'getRating')) {
                    continue;
                }
                
                $rating = $review->getRating();
                if (is_numeric($rating)) {
                    $total += (float) $rating;
                    $count++;
                }
            }
            
            return $count > 0 ? $total / $count : null;
            
        } catch (\Exception $e) {
            Logger::error("Error calculating customer rating: " . $e->getMessage());
            return null;
        }
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
