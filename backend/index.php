<?php
//php 8
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}
function calculation_mime_types($mimes) {
        $mimes['json'] = 'text/plain';
        return $mimes; 
    } 
add_filter('upload_mimes', 'calculation_mime_types');