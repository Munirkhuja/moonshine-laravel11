<?php

namespace App\Handlers;

use App\Exceptions\AnyToTelegramBotException;
use App\Models\ClientUser;
use App\Services\AuthService;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Stringable;

final class WebhookTelegramBotHandler extends WebhookHandler
{
    public function start(): void
    {
        $this->chat->storage()->forget('register_phone_check_code');
        $this->chat->message("Please enter you phone")
            ->send();
        $this->chat->storage()->set('register_phone', true);
    }

    /**
     * @throws AnyToTelegramBotException
     * @throws StorageException
     */
    protected function handleChatMessage(Stringable $text): void
    {
        if (!(new AuthService())->check_auth($this->chat->chat_id)
            && !($this->chat->storage()->get('register_phone')
                || $this->chat->storage()->get('register_phone_check_code'))
        ) {
            $this->start();
            return;
        }
        if ($this->chat->storage()->get('register_phone')) {
            $message = (new AuthService())->verifyPhone($text, $this->chat->chat_id);
            $this->chat->message($message)->send();
            $this->chat->storage()->forget('register_phone');
            $this->chat->storage()->set('register_phone_check_code', true);
            return;
        }
        if ($this->chat->storage()->get('register_phone_check_code')) {
            $message = (new AuthService())->setClient($text, $this->chat->chat_id);
            if ($message === true) {
                $this->chat->message('Successfully registered')->send();
                $this->chat->storage()->forget('register_phone_check_code');
            } else {
                $this->chat->message($message)->send();
            }
            return;
        }
        ClientUser::query()->where('t_chat_id', $this->chat->chat_id)
            ->active()->firstOrFail()
            ->client_user_messages()->create([
                't_chat_id' => $this->chat->chat_id,
                'message' => $text,
            ]);
    }
}
