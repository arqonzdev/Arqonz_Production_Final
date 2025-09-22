<?php
// src/AppBundle/Calculator/ExperienceCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Db;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;


class ProductPriceCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "ProductPrice") {
            $productName = $object->getProductName();
            $brand = $object->getBrand();
            $material = $object->getMaterial();
            $quantity = $object->getQuantity();
            
            $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
            $username = 'pimcoreuser';
            $password = 'G0H0me@T0day';
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT pr.ID FROM products pr
                    WHERE pr.Product_Type = :productName
                    AND pr.Product_Brand = :brand
                    AND pr.Product_Material = :material";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':productName', $productName);
            $stmt->bindValue(':brand', $brand);
            $stmt->bindValue(':material', $material);
            $stmt->execute();
            $productIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $minPrice = PHP_INT_MAX;
            $minPriceUnit = 'N/A';

            foreach ($productIds as $productId) {
                $priceSql = "SELECT Product_Price, Product_Unit FROM product_costs WHERE ID = :productId";
                $priceStmt = $pdo->prepare($priceSql);
                $priceStmt->bindValue(':productId', $productId);
                $priceStmt->execute();
                $priceData = $priceStmt->fetch(\PDO::FETCH_ASSOC);

                if ($priceData && $priceData['Product_Price'] < $minPrice) {
                    $minPrice = $priceData['Product_Price'];
                    $minPriceUnit = $priceData['Product_Unit'];
                }
            }

            $unitPrice = ($minPrice === PHP_INT_MAX) ? 'N/A' : $minPrice;
            $unit = $minPriceUnit;

            return $unitPrice;

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
