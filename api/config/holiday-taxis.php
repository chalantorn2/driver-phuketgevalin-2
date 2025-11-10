<?php
// api/config/holiday-taxis.php - Holiday Taxis API Configuration

class HolidayTaxisConfig
{
    // 🏢 Phuket Gevalin Configuration 
    const API_KEY = 'htscon_b6fede289eec88ec1a481ebdb689347e8d9353bafc037e74de7ffd2f1cf86a0f2ee5c0fd8d337304';
    const API_ENDPOINT = 'https://suppliers.holidaytaxis.com';
    const API_VERSION = '2025-01';

    /**
     * Get API headers for requests
     */
    public static function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . self::API_KEY,
            'X-API-Version: ' . self::API_VERSION
        ];
    }

    /**
     * Get full API URL
     */
    public static function getApiUrl($endpoint)
    {
        return self::API_ENDPOINT . '/' . ltrim($endpoint, '/');
    }
}
