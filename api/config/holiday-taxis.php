<?php
// api/config/holiday-taxis.php - Holiday Taxis API Configuration

class HolidayTaxisConfig
{
    // 🏢 Phuket Gevalin Configuration 
    const API_KEY = 'htscon_498d3538a201bd34019cd008a0d110ad1fc501c72cf5ed7a17fc20a7c2a36fe41c00c51778cba0ab';
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
