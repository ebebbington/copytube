<?php

//Optimize the autoloading (very important!)
//composer dumpautoload -o
//# Cache the Laravel routes (very important on slower servers)
//php artisan route:cache
//php artisan api:cache
//# Cache the core laravel configurations (including service providers, etc.)
//php artisan config:cache
//# Finally, tell Laravel to enable "production-ready optimizations."
//#php artisan optimize # No longer needed/available in Laravel 5.6.

$command =
    'composer dumpautoload -o' .
    '&&' .
    'php artisan route:cache' .
    '&&' .
    'php artisan cache:clear' .
    '&&' .
    'php artisan api:cache' .
    '&&' .
    'php artisan config:cache';
shell_exec($command);
