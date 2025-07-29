<?php

/**
 * Helper functions for multilingual routing
 */

use App\Models\Order;
use App\Services\Api;

if (!function_exists('localizedRoute')) {
    /**
     * Generate a localized route URL
     *
     * @param string $routeName
     * @param array $parameters
     * @param string|null $locale
     * @return string
     */
    function localizedRoute($routeName, $parameters = [], $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return route($routeName, $parameters);
        } else {
            return route($routeName . '.localized', array_merge(['locale' => $locale], $parameters));
        }
    }
}

if (!function_exists('localizedUrl')) {
    /**
     * Generate a localized URL
     *
     * @param string $path
     * @param string|null $locale
     * @return string
     */
    function localizedUrl($path, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return url($path);
        } else {
            return url('/' . $locale . $path);
        }
    }
}

if (!function_exists('getAlternateUrls')) {
    /**
     * Generate alternate URLs for hreflang tags
     *
     * @param string $path
     * @param array $parameters
     * @return array
     */
    function getAlternateUrls($path, $parameters = [])
    {
        $alternates = [];
        $languages = ['en', 'ar'];

        foreach ($languages as $lang) {
            if ($lang === 'en') {
                $alternates[$lang] = url($path);
            } else {
                $alternates[$lang] = url('/' . $lang . $path);
            }

            // Add parameters if provided
            if (!empty($parameters)) {
                $alternates[$lang] .= '?' . http_build_query($parameters);
            }
        }

        return $alternates;
    }
}

if (!function_exists('currentLanguageHome')) {
    /**
     * Get home URL for current language
     *
     * @return string
     */
    function currentLanguageHome()
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            return url('/');
        } else {
            return url('/' . $locale);
        }
    }
}

if (!function_exists('serviceUrl')) {
    /**
     * Generate service URL for current language
     *
     * @param int $serviceId
     * @param string|null $locale
     * @return string
     */
    function serviceUrl($serviceId, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return route('services.show.public', $serviceId);
        } else {
            return route('services.show.public.localized', ['locale' => $locale, 'service' => $serviceId]);
        }
    }
}

if (!function_exists('servicesUrl')) {
    /**
     * Generate all services URL for current language
     *
     * @param string|null $locale
     * @return string
     */
    function servicesUrl($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en') {
            return route('services.all');
        } else {
            return route('services.all.localized', ['locale' => $locale]);
        }
    }
}

if (!function_exists('checkWaitingOrdersAlert')) {
    /**
     * Check if there are waiting orders and return alert data for admins
     *
     * @return array|null
     */
    function checkWaitingOrdersAlert()
    {
        // Only show to admins
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return null;
        }

        $waitingOrdersCount = Order::where('status', 'waiting')->count();
        
        if ($waitingOrdersCount > 0) {
            try {
                $api = app(Api::class);
                $apiBalanceResponse = $api->balance();
                $apiBalance = $apiBalanceResponse->balance ?? 0;
                
                return [
                    'type' => 'warning',
                    'title' => __('adminlte.orders_waiting_for_api'),
                    'message' => __('adminlte.orders_waiting_message', ['count' => $waitingOrdersCount]),
                    'api_balance' => $apiBalance,
                    'waiting_orders_count' => $waitingOrdersCount,
                    'icon' => 'fas fa-exclamation-triangle'
                ];
            } catch (\Exception $e) {
                return [
                    'type' => 'danger',
                    'title' => 'API Connection Error',
                    'message' => __('adminlte.orders_waiting_message', ['count' => $waitingOrdersCount]) . ' ' . __('Unable to check API balance. Please verify API connection.'),
                    'waiting_orders_count' => $waitingOrdersCount,
                    'icon' => 'fas fa-exclamation-circle'
                ];
            }
        }
        
        return null;
    }
}

if (!function_exists('getApiBalance')) {
    /**
     * Get current API balance
     *
     * @return float|null
     */
    function getApiBalance()
    {
        try {
            $api = app(Api::class);
            $apiBalanceResponse = $api->balance();
            return $apiBalanceResponse->balance ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

