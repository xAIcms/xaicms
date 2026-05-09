<?php

class Markdown {
    public static function parse($text) {
        if (empty($text)) {
            return '';
        }

        // Standardize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove surrounding whitespace
        $text = trim($text);

        // 1. Code Blocks (Fenced) - Handle these first to avoid parsing content inside
        // Using a placeholder approach would be safer, but for simplicity, we'll try careful regex order.
        // Actually, let's just use a callback to escape content inside code blocks so other regexes don't touch it.
        // But that's complicated. Let's just do code blocks first and hope for the best with a simple parser.
        // A better way is to split the text into blocks.
        
        // Let's go with a line-by-line parser for blocks, it's more robust.
        
        $lines = explode("\n", $text);
        $output = '';
        $inCodeBlock = false;
        $codeBlockLang = '';
        $codeBlockContent = [];
        $inList = false;
        $listType = ''; // ul or ol
        
        foreach ($lines as $line) {
            // Fenced Code Blocks
            if (preg_match('/^```(\w*)$/', trim($line), $matches)) {
                if ($inCodeBlock) {
                    // End code block
                    $code = implode("\n", $codeBlockContent);
                    $output .= '<pre><code class="' . ($codeBlockLang ? 'language-' . $codeBlockLang : '') . '">' . htmlspecialchars($code) . "</code></pre>\n";
                    $inCodeBlock = false;
                    $codeBlockContent = [];
                    $codeBlockLang = '';
                } else {
                    // Start code block
                    $inCodeBlock = true;
                    $codeBlockLang = $matches[1];
                }
                continue;
            }
            
            if ($inCodeBlock) {
                $codeBlockContent[] = $line;
                continue;
            }
            
            // Lists
            $isListItem = false;
            if (preg_match('/^(\s*)-\s(.*)$/', $line, $matches)) {
                $isListItem = true;
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $output .= "</$listType>\n"; // Close previous list if different type
                    $output .= "<ul>\n";
                    $inList = true;
                    $listType = 'ul';
                }
                $output .= '<li>' . self::parseInline($matches[2]) . "</li>\n";
            } elseif (preg_match('/^(\s*)\d+\.\s(.*)$/', $line, $matches)) {
                $isListItem = true;
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $output .= "</$listType>\n";
                    $output .= "<ol>\n";
                    $inList = true;
                    $listType = 'ol';
                }
                $output .= '<li>' . self::parseInline($matches[2]) . "</li>\n";
            }
            
            if ($isListItem) continue;
            
            if ($inList) {
                $output .= "</$listType>\n";
                $inList = false;
                $listType = '';
            }
            
            // Headers
            if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $matches)) {
                $level = strlen($matches[1]);
                $output .= "<h{$level}>" . self::parseInline($matches[2]) . "</h{$level}>\n";
                continue;
            }
            
            // Blockquotes
            if (preg_match('/^>\s+(.*)$/', $line, $matches)) {
                $output .= "<blockquote>" . self::parseInline($matches[1]) . "</blockquote>\n";
                continue;
            }
            
            // Horizontal Rule
            if (preg_match('/^(\*{3,}|-{3,}|_{3,})$/', trim($line))) {
                $output .= "<hr>\n";
                continue;
            }
            
            // Empty lines (paragraph separation)
            if (trim($line) === '') {
                // Just skip empty lines, but maybe close a paragraph if we were building one?
                // In this simple line-by-line, we treat text lines as paragraphs.
                continue;
            }
            
            // Paragraphs
            // If it's just text, wrap in p.
            // Note: This simple parser wraps every line in P if it's not a block.
            // A real markdown parser would join consecutive lines.
            // Let's try to join consecutive text lines.
            // But for now, let's keep it simple: one line = one paragraph unless previous was text.
            // Actually, let's just output it as a paragraph.
            $output .= "<p>" . self::parseInline($line) . "</p>\n";
        }
        
        if ($inList) {
            $output .= "</$listType>\n";
        }
        
        return $output;
    }
    
    private static function parseInline($text) {
        // Images: ![alt](url)
        $text = preg_replace('/!\[(.*?)\]\((.*?)\)/', '<img src="$2" alt="$1" class="img-fluid">', $text);
        
        // Links: [text](url)
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $text);
        
        // Bold: **text** or __text__
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $text);
        
        // Italic: *text* or _text_
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/_(.*?)_/', '<em>$1</em>', $text);
        
        // Inline Code: `text`
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);
        
        return $text;
    }
}
