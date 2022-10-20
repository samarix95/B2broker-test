<?php

namespace App\Transactions\Transfer;

abstract class TransferAbstract
{
    const TRANSFER_OPERATION_NAME = "Transfer";

    public function __construct()
    {
    }

    public function transfer(): void
    {
    }
}
