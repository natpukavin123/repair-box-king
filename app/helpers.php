<?php

if (!function_exists('image_url')) {
    /**
     * Convert a stored image path (full URL or relative) to a display URL.
     * Delegates to ImageService::url().
     */
    function image_url(?string $path): ?string
    {
        return app(\App\Services\ImageService::class)->url($path);
    }
}
