<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class AnyToTelegramBotException extends Exception
{
    public function render(Request $request)
    {
        return response()->json(
            [
                'status' => false,
                'message' => $this->getMessage()
            ],
            $this->getCode()
        );
    }
}
