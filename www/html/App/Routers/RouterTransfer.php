<?php

namespace App\Routers;

use App\Output\Printer;
use App\Transactions\Transfer\TransferProvidersFactory;
use Exception;

class RouterTransfer extends RouterAbstract
{
    const TRANSFER_OPERATION_NAME = "Transfer";

    public function route($method, $formData, $urlData): void
    {
        if ($method == "POST") {
            if (count($formData) > 0) {
                try {
                    $provider = TransferProvidersFactory::create($formData);
                    $provider->transfer();
                } catch (Exception $e) {
                    Printer::printMessage($e->getMessage(), 500);
                }
            } else {
                Printer::printMessage("Got empty POST data", 400);
            }

            return;
        }

        Printer::printMessage("Method '$method' not found for route 'transfer'", 404);
    }
}
