<?php

namespace App\Transactions\Withdraw;

use App\Validator\ValidatoeWithdrawCC;
use App\Output\Printer;
use App\DB\FromFile;
use App\Exchanger\CurrencyExchanger;
use Exception;

class WithdrawCreditCards extends WithdrawAbstract
{
    /**
     * @var array
     */
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function withdraw(): void
    {
        $validator = new ValidatoeWithdrawCC($this->formData);
        $validator->validate();

        if ($validator->isValid()) {
            $tradingAccountId       = $validator->getValue("trading_account_id");
            $userId                 = $validator->getValue("user_id");
            $amountValue            = $validator->getValue("amount");

            $db                     = FromFile::getInstance();
            $data                   = $db->getData();
            $accountKey             = array_search($tradingAccountId, array_column($data["trading_accounts"], "id"));
            $tradingAccountBalance  = $data["trading_accounts"][$accountKey]["balance"];
            $tradingAccountCurrency = $data["trading_accounts"][$accountKey]["currency"];

            if ($validator->getValue("currency") != $tradingAccountCurrency) {
                $exchangeRate = CurrencyExchanger::getExchangeRate(
                    $validator->getValue("currency"),
                    $tradingAccountCurrency
                );
                $amountValue = $exchangeRate * $amountValue;
            }

            if ($tradingAccountBalance - $amountValue < 0) {
                throw new Exception("Can not process withdraw");
                return;
            }

            $this->process(
                $tradingAccountId,
                $userId,
                $tradingAccountBalance - $amountValue,
                $tradingAccountCurrency,
                $validator
            );

            Printer::printMessage("Successful withdraw", 200);
        } else {
            $errorMessage = $validator->getErrorMessages();
            Printer::printMessage($errorMessage, 400);
        }
    }
}
