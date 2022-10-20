<?php

require_once("./App/Autoloader.php");

use App\Api;

Autoloader::register();
Api::processApi();
