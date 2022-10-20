<?php

namespace App\Routers;

use App\DB\FromFile;
use App\Output\Printer;

class RouterAccountTransactionHistory extends RouterAbstract
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

            $accounts_transations = array_filter(
                $data["trading_accounts_transations"],
                function ($var) use ($accountId) {
                    return ($var["trading_account_id"] == $accountId);
                }
            );
            foreach (array_keys($accounts_transations) as $key) {
                unset($accounts_transations[$key]["user_id"]);
                unset($accounts_transations[$key]["trading_account_id"]);
            }

            if (count($accounts_transations) > 0) {
                if (isset($formData["sort_by"])
                    && in_array($formData["sort_by"], array_keys($accounts_transations[0]))
                ) {
                    usort($accounts_transations, function ($a, $b) use ($formData) {
                        return $a[$formData["sort_by"]] <=> $b[$formData["sort_by"]];
                    });
                }
            }

            $accounts_transations = array_values($accounts_transations);
            Printer::printJson($accounts_transations);

            return;
        }

        Printer::printMessage("Method '$method' not found for route 'get_users'", 404);
    }
}
