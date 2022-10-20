<?php

namespace App\Routers;

use App\Output\Printer;
use App\Transactions\Withdraw\WithdrawProvidersFactory;
use Exception;

class RouterWithdraw extends RouterAbstract
{
    public function route($method, $formData, $urlData): void
    {
        if ($method == "POST") {
            if (count($formData) > 0) {
                try {
                    $provider = WithdrawProvidersFactory::create($formData);
                    $provider->withdraw();
                } catch (Exception $e) {
                    Printer::printMessage($e->getMessage(), 500);
                }
            } else {
                Printer::printMessage("Got empty POST data", 400);
            }

            return;
        }

        Printer::printMessage("Method '$method' not found for route 'withdraw'", 404);
    }
}
