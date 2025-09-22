<?php
// src/AppBundle/Calculator/FeaturedImagePathCalculator.php

namespace App\Controller;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use \PDO;

class FeaturedImagePathCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "FeaturedImagePath") {
            $productName = $object->getProductName();
            
            if (!$productName) {
                return "/static/images/NewProdFeature.jpeg";
            }
            
            try {
                $dsn = 'mysql:host=localhost;dbname=pimcore;charset=utf8mb4';
                $username = 'pimcoreuser';
                $password = 'G0H0me@T0day';
                $pdo = new \PDO($dsn, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                $stmt = $pdo->prepare("SELECT Unique_ID FROM products WHERE Product_Name LIKE ? LIMIT 1");
                $stmt->execute(["%$productName%"]);
                $product = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($product) {
                    $uniqueId = $product['Unique_ID'];
                    
                    $stmt = $pdo->prepare("SELECT product_image_Path FROM product_images WHERE Unique_ID = ? LIMIT 1");
                    $stmt->execute([$uniqueId]);
                    $image = $stmt->fetch(\PDO::FETCH_ASSOC);

                    if ($image) {
                        $imagePath = str_replace('\\', '/', $image['product_image_Path']);
                        return "/static/images/Product_IMAGES" . $imagePath;
                    } else {
                        return "/static/images/NewProdFeature.jpeg";
                    }
                } else {
                    return "/static/images/NewProdFeature.jpeg";
                }
            } catch (\PDOException $e) {
                \Logger::error("Database error: " . $e->getMessage());
                return "Database error";
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
