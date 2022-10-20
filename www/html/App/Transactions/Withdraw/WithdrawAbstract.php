<?php

namespace App\Transactions\Withdraw;

use App\DB\FromFile;
use App\Validator\ValidatorAbstract;

abstract class WithdrawAbstract
{
    const WITHDRAW_OPERATION_NAME = "Withdraw";

    public function __construct()
    {
    }

    protected function withdraw(): void
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
                "operation"             => WithdrawAbstract::WITHDRAW_OPERATION_NAME,
                "summ"                  => "-" . $validator->getValue("amount"),
                "currency"              => $validator->getValue("currency"),
                "comment"               => $validator->getValue("comment"),
                "date"                  => date('Y-m-d H:i:s')
            ]
        );
    }
}
