<?php
// templates/partials/language_map.php

function getLanguageName($code) {
    $languages = [
        'zh-CN' => '中文 (简体)',
        'zh-TW' => '中文 (繁体)',
        'en-US' => 'English (US)',
        'en-GB' => 'English (UK)',
        'ja-JP' => '日本語',
        'ko-KR' => '한국어',
        'fr-FR' => 'Français',
        'de-DE' => 'Deutsch',
        'es-ES' => 'Español',
        'ru-RU' => 'Русский',
        'pt-BR' => 'Português',
        'it-IT' => 'Italiano',
        'vi-VN' => 'Tiếng Việt',
        'th-TH' => 'ไทย',
        'id-ID' => 'Bahasa Indonesia',
        'ar-SA' => 'العربية',
    ];

    // Try exact match
    if (isset($languages[$code])) {
        return $languages[$code];
    }

    // Try matching first part (e.g. 'en' from 'en-CA')
    $shortCode = explode('-', $code)[0];
    foreach ($languages as $key => $name) {
        if (strpos($key, $shortCode) === 0) {
            // Return generic name if possible, or just the mapped name
            // For simplicity, return the mapped name
            return $name;
        }
    }

    // Default: return the code itself uppercase
    return strtoupper($code);
}
