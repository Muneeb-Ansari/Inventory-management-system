<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $message = 'Insufficient stock available';

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->message,
                'status' => 422
            ], 422);
        }

        return back()->with('error', $this->message);
    }
}
