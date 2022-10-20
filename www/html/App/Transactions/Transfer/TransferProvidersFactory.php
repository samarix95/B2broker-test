<?php

namespace App\Transactions\Transfer;

use Exception;

class TransferProvidersFactory
{
    public static function create(array $formData): TransferAbstract
    {
        $method = $formData["transfer_method"];

        switch ($method) {
            case "INNER":
                return new InnerTransfer($formData);
                break;
            default:
                throw new Exception("Transfer method '$method' not found");
                break;
        }
    }
}
