<?php

namespace App\Repository;

class Currency
{
    const CURRENCY_USD = "USD";
    const CURRENCY_EUR = "EUR";
    const CURRENCY_GBP = "GBP";

    public static $availableCurrencies = [
        "USD",
        "EUR",
        "GBP",
    ];
}
