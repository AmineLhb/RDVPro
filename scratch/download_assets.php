<?php

$assets = [
    'public/vendor/tailwindcss/tailwind.js' => 'https://cdn.tailwindcss.com',
    'public/vendor/flatpickr/flatpickr.min.css' => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'public/vendor/flatpickr/flatpickr.min.js' => 'https://cdn.jsdelivr.net/npm/flatpickr',
    'public/vendor/flatpickr/fr.js' => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js',
    'public/vendor/alpinejs/alpine.min.js' => 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
    'public/vendor/fullcalendar/index.global.min.js' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
    'public/vendor/fullcalendar/fr.global.min.js' => 'https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/fr.global.min.js',
    'public/vendor/sweetalert2/sweetalert2.all.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
];

foreach ($assets as $path => $url) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    echo "Downloading $url into $path...\n";
    $content = file_get_contents($url);
    if ($content === false) {
        echo "Failed to download $url\n";
        exit(1);
    }
    file_put_contents($path, $content);
}

echo "All assets downloaded successfully!\n";
