<?php

namespace App\Routers;

use App\DB\FromFile;
use App\Output\Printer;

class RouterUsers extends RouterAbstract
{
    public function route($method, $formData, $urlData): void
    {
        if ($method == "GET") {
            $db = FromFile::getInstance();
            $data = $db->getData();
            Printer::printJson($data["users"]);
            return;
        }

        Printer::printMessage("Method '$method' not found for route 'get_users'", 404);
    }
}
