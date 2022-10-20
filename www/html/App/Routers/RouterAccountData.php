<?php

namespace App\Routers;

use App\DB\FromFile;
use App\Output\Printer;

class RouterAccountData extends RouterAbstract
{
    public function route($method, $formData, $urlData): void
    {
        if ($method == "GET") {
            if (count($urlData) == 0 || $urlData[0] == "") {
                Printer::printMessage("Please, provide the account id", 400);
                return;
            }

            $db         = FromFile::getInstance();
            $data       = $db->getData();
            $accountId  = $urlData[0];

            $accountKey = array_search($accountId, array_column($data["trading_accounts"], "id"));
            if ($accountKey === false) {
                Printer::printMessage("Account with ID: '$accountId' not found ", 404);
                return;
            }

            $accoounts = $data["trading_accounts"];
            unset($accoounts[$accountKey]["id"]);
            unset($accoounts[$accountKey]["user_id"]);
            Printer::printJson($accoounts[$accountKey]);
            return;
        }

        Printer::printMessage("Method '$method' not found for route 'get_users'", 404);
    }
}
