<?php

namespace App\Transactions\Deposit;

use App\Validator\ValidatoeDepositCC;
use App\Output\Printer;
use App\DB\FromFile;
use App\Exchanger\CurrencyExchanger;

class DepositCreditCards extends DepositAbstract
{
    /**
     * @var array
     */
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function deposit(): void
    {
        $validator = new ValidatoeDepositCC($this->formData);
        $validator->validate();

        if ($validator->isValid()) {
            $db                     = FromFile::getInstance();
            $data                   = $db->getData();

            $tradingAccountId       = $validator->getValue("trading_account_id");
            $userId                 = $validator->getValue("user_id");
            $amountValue            = $validator->getValue("amount");

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

            $this->process(
                $tradingAccountId,
                $userId,
                $tradingAccountBalance + $amountValue,
                $tradingAccountCurrency,
                $validator
            );

            Printer::printMessage("Successful deposited", 200);
        } else {
            $errorMessage = $validator->getErrorMessages();
            Printer::printMessage($errorMessage, 400);
        }
    }
}
