<?php

return [
    'cloud_url' => env('CLOUDINARY_URL'),
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'laravel_unsigned'),
    // Opcional, pero ponlo si quieres
    'notification_url' => '',
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => true,
    'shorten' => false,
];