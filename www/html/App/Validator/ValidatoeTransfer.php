<?php

namespace App\Validator;

use App\DB\FromFile;
use Exception;

class ValidatoeTransfer extends ValidatorAbstract
{
    protected $mappers = [
        [
            "field"         => "trading_account_from",
            "functionName"  => "validateTradingAccount",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "trading_account_to",
            "functionName"  => "validateTradingAccount",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "amount",
            "functionName"  => "validateDepositAmount",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "currency",
            "functionName"  => "isCurrencyAvailable",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "comment",
            "functionName"  => "",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => false,
        ],
    ];

    protected function validateTradingAccount($value): bool
    {
        if (is_null($value) || $value == "") {
            throw new Exception("Please, provide the trading account id");
        }

        $db     = FromFile::getInstance();
        $data   = $db->getData();

        $accountKey = array_search($value, array_column($data["trading_accounts"], "id"));
        if ($accountKey === false) {
            throw new Exception("Account '$value' not found");
        }

        return true;
    }

    protected function validateDepositAmount($value): bool
    {
        if (!is_numeric($value)) {
            throw new Exception("Incorrenct value '$value'");
        }

        if ($value < 0) {
            throw new Exception("Negative value '$value'");
        }

        return true;
    }
}
