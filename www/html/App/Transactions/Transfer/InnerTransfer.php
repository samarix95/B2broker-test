<?php

namespace App\Transactions\Transfer;

use App\Validator\ValidatoeTransfer;
use App\Exchanger\CurrencyExchanger;
use App\Output\Printer;
use App\DB\FromFile;
use Exception;

class InnerTransfer extends TransferAbstract
{
    /**
     * @var array
     */
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function transfer(): void
    {
        $validator = new ValidatoeTransfer($this->formData);
        $validator->validate();

        if ($validator->isValid()) {
            $db = FromFile::getInstance();
            $data = $db->getData();

            $tradingAccountFrom = $validator->getValue("trading_account_from");
            $tradingAccountTo = $validator->getValue("trading_account_to");
            $amountValue = $validator->getValue("amount");

            $accountKey = array_search($tradingAccountFrom, array_column($data["trading_accounts"], "id"));
            $tradingAccountFromBalance = $data["trading_accounts"][$accountKey]["balance"];
            $tradingAccountFromCurrency = $data["trading_accounts"][$accountKey]["currency"];
            $tradingAccountFromUserId = $data["trading_accounts"][$accountKey]["user_id"];

            $accountKey = array_search($tradingAccountTo, array_column($data["trading_accounts"], "id"));
            $tradingAccountToBalance = $data["trading_accounts"][$accountKey]["balance"];
            $tradingAccountToCurrency = $data["trading_accounts"][$accountKey]["currency"];
            $tradingAccountToUserId = $data["trading_accounts"][$accountKey]["user_id"];

            if ($validator->getValue("currency") != $tradingAccountToCurrency) {
                $exchangeRate = CurrencyExchanger::getExchangeRate(
                    $validator->getValue("currency"),
                    $tradingAccountToCurrency
                );
                $amountValue = $exchangeRate * $amountValue;
            }

            if ($tradingAccountFromBalance - $amountValue < 0) {
                throw new Exception("Can not process withdraw");
                return;
            }

            $db->updateData(
                "trading_accounts",
                $tradingAccountFrom,
                [
                    "id"        => $tradingAccountFrom,
                    "user_id"   => $tradingAccountFromUserId,
                    "balance"   => $tradingAccountFromBalance - $amountValue,
                    "currency"  => $tradingAccountFromCurrency
                ]
            );

            $db->insertData(
                "trading_accounts_transations",
                [
                    "user_id"               => $tradingAccountFromUserId,
                    "trading_account_id"    => $tradingAccountFrom,
                    "operation"             => TransferAbstract::TRANSFER_OPERATION_NAME,
                    "summ"                  => "-" . $validator->getValue("amount"),
                    "currency"              => $validator->getValue("currency"),
                    "comment"               => $validator->getValue("comment"),
                    "date"                  => date('Y-m-d H:i:s')
                ]
            );

            $db->updateData(
                "trading_accounts",
                $tradingAccountTo,
                [
                    "id"        => $tradingAccountTo,
                    "user_id"   => $tradingAccountToUserId,
                    "balance"   => $tradingAccountToBalance + $amountValue,
                    "currency"  => $tradingAccountToCurrency
                ]
            );

            $db->insertData(
                "trading_accounts_transations",
                [
                    "user_id"               => $tradingAccountToUserId,
                    "trading_account_id"    => $tradingAccountTo,
                    "operation"             => TransferAbstract::TRANSFER_OPERATION_NAME,
                    "summ"                  => "+" . $validator->getValue("amount"),
                    "currency"              => $validator->getValue("currency"),
                    "comment"               => $validator->getValue("comment"),
                    "date"                  => date('Y-m-d H:i:s')
                ]
            );

            Printer::printMessage("Successful transfered", 200);
        } else {
            $errorMessage = $validator->getErrorMessages();
            Printer::printMessage($errorMessage, 400);
        }
    }
}
