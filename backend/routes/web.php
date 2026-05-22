<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Tap Terminal API',
        'version' => '1.0.0',
        'swagger' => url('/api/documentation'),
    ]);
});
