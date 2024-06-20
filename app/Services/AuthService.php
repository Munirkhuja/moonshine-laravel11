<?php

namespace App\Services;

use App\Enums\StatusClientUserEnum;
use App\Exceptions\AnyToTelegramBotException;
use App\Jobs\SendSMS;
use App\Models\ClientRegister;
use App\Models\ClientUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * @throws \Exception
     */
    public function verifyPhone($phone, $chat_id): \Illuminate\Foundation\Application|array|string|\Illuminate\Contracts\Translation\Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        $check_sms_count = config('test-app.limit_check_code');
        $minute = config('test-app.limit_minute_to_verification_code');

        $client = ClientUser::query()->where('phone', $phone)->first();
        if ($client && $client->status === StatusClientUserEnum::BLOCKED) {
            return trans('message.not_active');
        }
        $register = ClientRegister::where('phone', $phone)
            ->where('t_chat_id', $chat_id)
            ->orderByDesc('id')->first();
        if ($register && $register->count > 1
            && $register->updated_at->diffInMinutes() < $minute
        ) {
            $past_seconds = $register->updated_at->diffInSeconds();
            $seconds = config('test-app.limit_seconds_to_registers');
            if (app()->isProduction()) {
                $seconds *= ($check_sms_count - $register->count + 1) ** 2;
            }
            if ($past_seconds < $seconds) {
                $sec = $seconds - $past_seconds;

                return trans('message.unique_try', ['sec' => $sec]);
            }
            $register->decrement('count');
            $register->save();
            SendSMS::dispatch($phone, trans('message.sms.sms_code', ['sms_code' => $register->code]));
            return 'Enter the code sent to your phone';
        }
        $code = (string)random_int(1000, 9999);
        SendSMS::dispatch($phone, trans('message.sms.sms_code', ['sms_code' => $code]));

        $register = new ClientRegister();
        $register->t_chat_id = $chat_id;
        $register->phone = $phone;
        $register->code = $code;
        $register->count = $check_sms_count;
        $register->save();

        return 'Enter the code sent to your phone';
    }

    /**
     * @throws AnyToTelegramBotException
     */
    public function setClient($code, $chat_id): true|\Illuminate\Foundation\Application|array|string|\Illuminate\Contracts\Translation\Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        $register = ClientRegister::query()
            ->where('t_chat_id', $chat_id)
            ->orderByDesc('id')
            ->first();
        if (!$register) {
            return trans('message.sms_code_not_found') . '1';
        }
        $minute = config('test-app.limit_minute_to_verification_code');

        if ($register->count <= 0) {
            return trans('message.many_try') . ' ' . trans('message.try_again_step_1');
        }

        if ($register->updated_at->diffInMinutes() > $minute) {
            return trans('message.try_again_step_1');
        }
        if ($register->code !== (string)$code) {
            $register->decrement('count');
            $register->save();

            return trans('message.sms_code_not_found');
        }

        try {
            DB::beginTransaction();
            $user = ClientUser::where('phone', $register->phone)
                ->where('t_chat_id', $chat_id)
                ->first();
            if (!$user) {
                $user = new ClientUser();
                $user->t_chat_id = $chat_id;
                $user->phone = $register->phone;
                $user->status = StatusClientUserEnum::ACTIVE;
                $user->save();
            }
            $register->count = 0;
            $register->save();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());

            return trans('message.error_server');
        }
    }

    public function check_auth($chat_id): bool
    {
        if (!ClientUser::query()->where('t_chat_id', $chat_id)->first()) {
            return false;
        }

        return true;
    }
}
