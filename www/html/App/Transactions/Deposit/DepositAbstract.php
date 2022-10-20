<?php

namespace App\Transactions\Deposit;

use App\DB\FromFile;
use App\Validator\ValidatorAbstract;

abstract class DepositAbstract
{
    const DEPOSIT_OPERATION_NAME = "Deposit";

    public function __construct()
    {
    }

    protected function deposit(): void
    {
    }

    protected function process(
        int $tradingAccountId,
        int $userId,
        float $newBalance,
        string $tradingAccountCurrency,
        ValidatorAbstract $validator
    ): void {
        $db = FromFile::getInstance();

        $db->updateData(
            "trading_accounts",
            $tradingAccountId,
            [
                "id"        => $tradingAccountId,
                "user_id"   => $userId,
                "balance"   => $newBalance,
                "currency"  => $tradingAccountCurrency
            ]
        );

        $db->insertData(
            "trading_accounts_transations",
            [
                "user_id"               => $userId,
                "trading_account_id"    => $tradingAccountId,
                "operation"             => self::DEPOSIT_OPERATION_NAME,
                "summ"                  => "+" . $validator->getValue("amount"),
                "currency"              => $validator->getValue("currency"),
                "comment"               => $validator->getValue("comment"),
                "date"                  => date('Y-m-d H:i:s')
            ]
        );
    }
}
