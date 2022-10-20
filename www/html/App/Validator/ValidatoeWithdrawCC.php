<?php

namespace App\Validator;

use App\DB\FromFile;
use Exception;

class ValidatoeWithdrawCC extends ValidatorAbstract
{
    protected $mappers = [
        [
            "field"         => "user_id",
            "functionName"  => "validateUser",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "trading_account_id",
            "functionName"  => "validateTradingAccount",
            "value"         => null,
            "errorMessage"  => null,
            "isRequired"    => true,
        ],
        [
            "field"         => "amount",
            "functionName"  => "validateWithdrawAmount",
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

    protected function validateUser($value): bool
    {
        if (is_null($value) || $value == "") {
            throw new Exception("Please, provide the user's id");
        }

        $db     = FromFile::getInstance();
        $data   = $db->getData();

        if (array_search($value, array_column($data["users"], "id")) === false) {
            throw new Exception("User with ID: '$value' not found");
        }

        return true;
    }

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

    protected function validateWithdrawAmount($value): bool
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
