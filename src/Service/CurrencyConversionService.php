<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CurrencyConversionService
{
    private $logger;
    private $cache = [];
    private $cacheExpiry = 3600; // 1 hour cache

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get USD to INR conversion rate
     * Uses exchangerate-api.com (free tier: 1500 requests/month)
     */
    public function getUsdToInrRate(): float
    {
        $cacheKey = 'usd_to_inr_rate';
        
        // Check cache first
        if (isset($this->cache[$cacheKey]) && 
            (time() - $this->cache[$cacheKey]['timestamp']) < $this->cacheExpiry) {
            return $this->cache[$cacheKey]['rate'];
        }

        try {
            // Using exchangerate-api.com free API with cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.exchangerate-api.com/v4/latest/USD');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CurrencyService/1.0)');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response !== false && $httpCode === 200) {
                $data = json_decode($response, true);
                
                if (isset($data['rates']['INR'])) {
                    $rate = (float) $data['rates']['INR'];
                    
                    // Cache the result
                    $this->cache[$cacheKey] = [
                        'rate' => $rate,
                        'timestamp' => time()
                    ];
                    
                    $this->logger->info('Currency conversion rate fetched', ['rate' => $rate]);
                    return $rate;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch currency conversion rate', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback rate if API fails (approximate rate)
        return 83.0;
    }

    /**
     * Convert INR amount to USD
     */
    public function convertInrToUsd(float $inrAmount): float
    {
        $rate = $this->getUsdToInrRate();
        return round($inrAmount / $rate, 2);
    }

    /**
     * Convert USD amount to INR
     */
    public function convertUsdToInr(float $usdAmount): float
    {
        $rate = $this->getUsdToInrRate();
        return round($usdAmount * $rate, 2);
    }

    /**
     * Check if customer is from India based on phone country code
     */
    public function isIndianCustomer($phoneCountry): bool
    {
        // If phoneCountry is empty or null, consider as India (91)
        if (empty($phoneCountry)) {
            return true;
        }
        
        return $phoneCountry === '91';
    }

    /**
     * Get currency symbol based on customer location
     */
    public function getCurrencySymbol($phoneCountry): string
    {
        return $this->isIndianCustomer($phoneCountry) ? '₹' : '$';
    }

    /**
     * Get currency code based on customer location
     */
    public function getCurrencyCode($phoneCountry): string
    {
        return $this->isIndianCustomer($phoneCountry) ? 'INR' : 'USD';
    }

    /**
     * Get tax label based on customer location
     */
    public function getTaxLabel($phoneCountry): string
    {
        return $this->isIndianCustomer($phoneCountry) ? 'GST' : 'VAT';
    }
}
