<?php

namespace App\Routers;

use Exception;

class RouterFactory
{
    public static function create(string $router): RouterAbstract
    {
        switch ($router) {
            case "get_users":
                return new RouterUsers();
                break;
            case "get_user_accounts":
                return new RouterUserAccounts();
                break;
            case "get_account_data":
                return new RouterAccountData();
                break;
            case "get_account_transaction_history":
                return new RouterAccountTransactionHistory();
                break;
            case "deposit":
                return new RouterDeposit();
                break;
            case "withdraw":
                return new RouterWithdraw();
                break;
            case "transfer":
                return new RouterTransfer();
                break;
            default:
                throw new Exception("Route $router not found");
                break;
        }
    }
}
