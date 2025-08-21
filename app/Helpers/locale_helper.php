<?php
if (!function_exists('getOgLocale')) {
    function getOgLocale()
    {
        $locales = [
            'ar' => 'ar_AR',
            'es' => 'es_ES',
            'zh' => 'zh_CN',
            'hi' => 'hi_IN',
            'pt' => 'pt_PT',
            'ru' => 'ru_RU',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'en' => 'en_US', // Default for English
        ];

        return $locales[app()->getLocale()] ?? 'en_US';
    }
}
