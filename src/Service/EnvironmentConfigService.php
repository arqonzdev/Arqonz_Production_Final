<?php

namespace App\Service;

class EnvironmentConfigService
{
    private static $config = null;
    
    /**
     * Get environment configuration
     */
    public static function getConfig(): array
    {
        if (self::$config === null) {
            self::$config = self::loadConfig();
        }
        
        return self::$config;
    }
    
    /**
     * Get a specific configuration value
     */
    public static function get(string $key, $default = null)
    {
        $config = self::getConfig();
        return $config[$key] ?? $default;
    }
    
    /**
     * Load configuration from environment files
     */
    private static function loadConfig(): array
    {
        $config = [];
        
        // Load from config/local/.env if it exists
        $envFile = __DIR__ . '/../../config/local/.env';
        if (file_exists($envFile)) {
            $config = array_merge($config, self::parseEnvFile($envFile));
        }
        
        // Load from .env.local if it exists (Symfony standard)
        $envLocalFile = __DIR__ . '/../../.env.local';
        if (file_exists($envLocalFile)) {
            $config = array_merge($config, self::parseEnvFile($envLocalFile));
        }
        
        // Override with actual environment variables
        $config = array_merge($config, $_ENV);
        
        return $config;
    }
    
    /**
     * Parse .env file
     */
    private static function parseEnvFile(string $file): array
    {
        $config = [];
        
        if (!file_exists($file)) {
            return $config;
        }
        
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue; // Skip comments
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                $config[$key] = $value;
            }
        }
        
        return $config;
    }
    
    /**
     * Get reCAPTCHA configuration
     */
    public static function getRecaptchaConfig(): array
    {
        return [
            'site_key' => self::get('RECAPTCHA_SITE_KEY'),
            'secret_key' => self::get('RECAPTCHA_SECRET_KEY')
        ];
    }
    
    /**
     * Get Gupshup WhatsApp configuration
     */
    public static function getGupshupConfig(): array
    {
        return [
            'api_url' => self::get('GUPSHUP_API_URL', 'https://api.gupshup.io/wa/api/v1/template/msg'),
            'api_key' => self::get('GUPSHUP_API_KEY'),
            'source_number' => self::get('GUPSHUP_SOURCE_NUMBER'),
            'source_name' => self::get('GUPSHUP_SOURCE_NAME')
        ];
    }
    
    /**
     * Get Razorpay configuration
     */
    public static function getRazorpayConfig(): array
    {
        return [
            'key_id' => self::get('RAZORPAY_KEY_ID'),
            'key_secret' => self::get('RAZORPAY_KEY_SECRET')
        ];
    }
    
    /**
     * Get OpenAI configuration
     */
    public static function getOpenAIConfig(): array
    {
        return [
            'api_key' => self::get('OPENAI_API_KEY'),
            'api_url' => self::get('OPENAI_API_URL', 'https://api.openai.com/v1/threads')
        ];
    }
    
    /**
     * Get WhatsApp API configuration
     */
    public static function getWhatsAppConfig(): array
    {
        return [
            'api_url' => self::get('WHATSAPP_API_URL'),
            'api_key' => self::get('WHATSAPP_API_KEY')
        ];
    }
}
