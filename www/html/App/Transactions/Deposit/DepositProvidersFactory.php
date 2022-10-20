<?php

namespace App\Transactions\Deposit;

use Exception;

class DepositProvidersFactory
{
    public static function create(array $formData): DepositAbstract
    {
        $method = $formData["payment_method"];

        switch ($method) {
            case "CC":
                return new DepositCreditCards($formData);
                break;
            default:
                throw new Exception("Payment deposit method '$method' not found");
                break;
        }
    }
}
