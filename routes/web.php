<?php

use Illuminate\Support\Facades\Route;

Route::post('/telegraph/{token}/custom-webhook', [\App\Http\Controllers\TelegramBotController::class, 'handle'])
    ->middleware(config('telegraph.webhook.middleware', []),)
    ->name('telegraph.webhook');
