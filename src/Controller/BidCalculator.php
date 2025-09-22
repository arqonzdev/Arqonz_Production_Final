<?php
// src/AppBundle/Calculator/BidCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class BidCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if (in_array($context->getFieldname(), ["L1", "L2", "L3"])) {
            // Get the reverse relations to SupplierBids
            $supplierBids = $object->getSupplierBid();
            
            if (!empty($supplierBids)) {
                // Sort SupplierBids based on BidAmount, EndDate, and CreationDate
                usort($supplierBids, function ($a, $b) {
                    $bidAmountComparison = $a->getBidAmount() <=> $b->getBidAmount();
                    if ($bidAmountComparison !== 0) {
                        return $bidAmountComparison;
                    }
                    // If BidAmount is the same, compare by EndDate
                    $endDateComparison = $a->getEndDate() <=> $b->getEndDate();
                    if ($endDateComparison !== 0) {
                        return $endDateComparison;
                    }
                    // If EndDate is also the same, compare by CreationDate
                    return $a->getCreationDate() <=> $b->getCreationDate();
                });

                // Determine L1, L2, and L3 based on sorted results
                $l1BidKey = isset($supplierBids[0]) ? $supplierBids[0]->getKey() : "-";
                $l2BidKey = isset($supplierBids[1]) ? $supplierBids[1]->getKey() : "-";
                $l3BidKey = isset($supplierBids[2]) ? $supplierBids[2]->getKey() : "-";

                switch ($context->getFieldname()) {
                    case "L1":
                        return $l1BidKey;
                    case "L2":
                        return $l2BidKey;
                    case "L3":
                        return $l3BidKey;
                }
            } else {
                return "-";
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