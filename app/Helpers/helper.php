<?php

/**
 * Helper functions for multilingual routing
 */

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
            return route('service.show', $serviceId);
        } else {
            return route('service.show.localized', ['locale' => $locale, 'serviceId' => $serviceId]);
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

