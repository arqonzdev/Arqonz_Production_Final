<?php
// src/AppBundle/Calculator/BidAmountCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\SupplierBid;

class BidAmountCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        $field = $context->getFieldname();

        // Determine which L field to reference for the corresponding amount
        $lField = null;
        switch ($field) {
            case "L1Amt":
                $lField = $object->getL1();
                break;
            case "L2Amt":
                $lField = $object->getL2();
                break;
            case "L3Amt":
                $lField = $object->getL3();
                break;
            default:
                \Logger::error("Unknown field: " . $field);
                return "Error";
        }

        if ($lField === "-") {
            // If the corresponding L field is "-", return "-"
            return "-";
        }

        if ($lField) {
            // Construct the path to the SupplierBid object
            $path = "/SupplierBid/" . $lField;
            $supplierBid = SupplierBid::getByPath($path);

            if ($supplierBid instanceof SupplierBid) {
                // Return the BidAmount of the respective SupplierBid
                return (string)$supplierBid->getBidAmount();
            } else {
                \Logger::error("SupplierBid not found at path: " . $path);
                return "-";
            }
        } else {
            return "-";
        }
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
