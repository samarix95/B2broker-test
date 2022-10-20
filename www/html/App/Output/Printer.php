<?php

namespace App\Output;

class Printer
{
    /**
     * @param array $data
     * @return void
     */
    public static function printJson(array $data): void
    {
        header('Content-type: application/json');
        http_response_code(200);
        echo json_encode($data);
    }

    /**
     * @param string $data
     * @param int $statusCode
     * @return void
     */
    public static function printMessage(string $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo ($data);
    }
}
