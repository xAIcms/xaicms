<?php
// src/Utils/SlugGenerator.php

class SlugGenerator {
    public static function generate($string) {
        $slug = trim($string);

        // Try to use intl extension for Pinyin/Transliteration
        if (function_exists('transliterator_transliterate')) {
            // "Any-Latin; Latin-ASCII; Lower()"
            // Any-Latin: Converts script to Latin (e.g. Chinese -> Pinyin)
            // Latin-ASCII: Removes accents (e.g. Pinyin tones)
            // Lower: Converts to lowercase
            $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $slug);
            if ($transliterated !== false) {
                $slug = $transliterated;
            }
        }

        // Standard cleanup
        // Replace spaces with dashes
        $slug = preg_replace('/\s+/u', '-', $slug);
        
        // Remove unsafe chars (keep letters, numbers, dashes)
        // We allow unicode letters if transliteration failed or wasn't perfect,
        // but typically we want ASCII for slugs if possible. 
        // However, to remain safe for environments without intl, we keep \p{L}.
        $slug = preg_replace('/[^\p{L}\p{N}\p{M}-]+/u', '', $slug);
        
        // Dedupe dashes
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Lowercase
        $slug = mb_strtolower($slug, 'UTF-8');

        return $slug;
    }
}
