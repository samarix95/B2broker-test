<?php

namespace App\Routers;

use App\Output\Printer;
use App\Transactions\Deposit\DepositProvidersFactory;
use Exception;

class RouterDeposit extends RouterAbstract
{
    public function route($method, $formData, $urlData): void
    {
        if ($method == "POST") {
            if (count($formData) > 0) {
                try {
                    $provider = DepositProvidersFactory::create($formData);
                    $provider->deposit();
                } catch (Exception $e) {
                    Printer::printMessage($e->getMessage(), 500);
                }
            } else {
                Printer::printMessage("Got empty POST data", 400);
            }

            return;
        }

        Printer::printMessage("Method '$method' not found for route 'deposit'", 404);
    }
}
