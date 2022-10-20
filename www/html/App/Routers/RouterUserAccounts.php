<?php

namespace App\Routers;

use App\DB\FromFile;
use App\Output\Printer;

class RouterUserAccounts extends RouterAbstract
{
    public function route($method, $formData, $urlData): void
    {
        if ($method == "GET") {
            if (count($urlData) == 0 || $urlData[0] == "") {
                Printer::printMessage("Please, provide the user's id", 400);
                return;
            }

            $db     = FromFile::getInstance();
            $data   = $db->getData();
            $userId = $urlData[0];

            if (array_search($userId, array_column($data["users"], "id")) === false) {
                Printer::printMessage("User with ID: '$userId' not found ", 404);
                return;
            }

            $accoounts = array_filter(
                $data["trading_accounts"],
                function ($var) use ($userId) {
                    return ($var["user_id"] == $userId);
                }
            );
            $accoounts = array_values($accoounts);
            foreach (array_keys($accoounts) as $key) {
                unset($accoounts[$key]["user_id"]);
            }
            Printer::printJson($accoounts);
            return;
        }

        Printer::printMessage("Method '$method' not found for route 'get_users'", 404);
    }
}
