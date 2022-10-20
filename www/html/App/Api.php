<?php

namespace App;

use App\Routers\RouterFactory;
use App\Output\Printer;
use Exception;

class Api
{

    public static function processApi(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $formData = self::getFormData($method);

            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $urls = explode('/', $url);
            $urlData = array_slice($urls, 2);

            $router = RouterFactory::create($urls[1]);
            $router->route($method, $formData, $urlData);
        } catch (Exception $e) {
            Printer::printMessage($e->getMessage(), 500);
        }
    }

    /**
     * @param string $method
     * @return array
     */
    private static function getFormData(string $method): array
    {
        if ($method == 'GET') {
            return $_GET;
        }

        if ($method == 'POST') {
            $postData = $_POST;
            if (count($postData) == 0) {
                if (strlen(file_get_contents('php://input')) > 0) {
                    $postData = file_get_contents('php://input');
                    $postData = json_decode($postData, true);
                }
            }
            return $postData;
        }

        $data = [];
        $exploded = explode('&', file_get_contents('php://input'));

        foreach ($exploded as $pair) {
            $item = explode('=', $pair);
            if (count($item) == 2) {
                $data[urldecode($item[0])] = urldecode($item[1]);
            }
        }

        return $data;
    }
}
