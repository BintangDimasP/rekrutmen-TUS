<?php

if (!function_exists('file_url')) {
    function file_url($path): string
    {
        if (empty($path)) {
            return '#';
        }
        $str = (string) $path;
        if (str_starts_with($str, 'http://') || str_starts_with($str, 'https://')) {
            return $str;
        }
        return asset('storage/' . ltrim($str, '/'));
    }
}
