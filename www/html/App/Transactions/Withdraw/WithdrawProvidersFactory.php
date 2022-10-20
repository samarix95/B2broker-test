<?php

namespace App\Transactions\Withdraw;

use Exception;

class WithdrawProvidersFactory
{
    public static function create(array $formData): WithdrawAbstract
    {
        $method = $formData["payment_method"];

        switch ($method) {
            case "CC":
                return new WithdrawCreditCards($formData);
                break;
            default:
                throw new Exception("Payment withdraw method '$method' not found");
                break;
        }
    }
}
