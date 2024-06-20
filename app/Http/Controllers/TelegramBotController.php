<?php

namespace App\Http\Controllers;

use App\Handlers\WebhookTelegramBotHandler;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function handle(Request $request, string $token): void
    {
        $botModel = config('telegraph.models.bot');
        $bot = $botModel::fromToken($token);

        (new WebhookTelegramBotHandler())->handle($request, $bot);
    }
}
