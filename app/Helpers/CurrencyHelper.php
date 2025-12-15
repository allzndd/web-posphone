<?php

use App\Models\OwnerSetting;

if (!function_exists('get_currency')) {
    /**
     * Get current owner's currency setting
     *
     * @return string
     */
    function get_currency()
    {
        $user = auth()->user();
        if (!$user || !$user->owner) {
            return 'IDR';
        }

        $settings = OwnerSetting::where('owner_id', $user->owner->id)->first();
        return $settings ? $settings->currency : 'IDR';
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @param string|null $currency
     * @return string
     */
    function get_currency_symbol($currency = null)
    {
        $currency = $currency ?? get_currency();
        
        return match($currency) {
            'IDR' => 'Rp',
            'MYR' => 'RM',
            'USD' => '$',
            default => 'Rp',
        };
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format number as currency with symbol
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    function format_currency($amount, $currency = null)
    {
        $currency = $currency ?? get_currency();
        $symbol = get_currency_symbol($currency);
        
        // Format based on currency
        switch ($currency) {
            case 'IDR':
                // Indonesian: Rp 100.000
                return $symbol . ' ' . number_format($amount, 0, ',', '.');
            
            case 'MYR':
            case 'USD':
                // Malaysian & US: RM 100.00 or $ 100.00
                return $symbol . ' ' . number_format($amount, 2, '.', ',');
            
            default:
                return $symbol . ' ' . number_format($amount, 0, ',', '.');
        }
    }
}

if (!function_exists('get_decimal_places')) {
    /**
     * Get decimal places for currency
     *
     * @param string|null $currency
     * @return int
     */
    function get_decimal_places($currency = null)
    {
        $currency = $currency ?? get_currency();
        
        return match($currency) {
            'IDR' => 0,
            'MYR' => 2,
            'USD' => 2,
            default => 0,
        };
    }
}

if (!function_exists('get_currency_step')) {
    /**
     * Get step value for currency input
     *
     * @param string|null $currency
     * @return string
     */
    function get_currency_step($currency = null)
    {
        $currency = $currency ?? get_currency();
        
        return match($currency) {
            'IDR' => '1',
            'MYR' => '0.01',
            'USD' => '0.01',
            default => '1',
        };
    }
}
