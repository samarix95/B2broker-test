<?php

namespace App\Exchanger;

use App\Repository\Currency;

class CurrencyExchanger
{
    public static $rates = [
        Currency::CURRENCY_USD => [
            [
                "to"    => Currency::CURRENCY_EUR,
                "rate"  => 1.1,
            ],
            [
                "to"    => Currency::CURRENCY_GBP,
                "rate"  => 1.8,
            ],
        ],
        Currency::CURRENCY_EUR => [
            [
                "to"    => Currency::CURRENCY_USD,
                "rate"  => 0.9,
            ],
            [
                "to"    => Currency::CURRENCY_GBP,
                "rate"  => 0.6,
            ],
        ],
        Currency::CURRENCY_GBP => [
            [
                "to"    => Currency::CURRENCY_USD,
                "rate"  => 1.9,
            ],
            [
                "to"    => Currency::CURRENCY_EUR,
                "rate"  => 2.1,
            ],
        ],
    ];

    public static function getExchangeRate(string $from, string $to): float
    {
        $key = array_search($to, array_column(self::$rates[$from], "to"));
        return self::$rates[$from][$key]["rate"];
    }
}
