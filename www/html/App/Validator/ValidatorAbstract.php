<?php

namespace App\Validator;

use App\Repository\Currency;
use Exception;

abstract class ValidatorAbstract
{
    /**
     * @var array
     */
    protected $mappers = [];

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(): void
    {
        foreach ($this->mappers as $mapKey => $map) {
            if (!$map["isRequired"]) {
                $value = $this->data[$map["field"]];
                $this->mappers[$mapKey]["value"] = $value;
            }

            if (method_exists($this, $map["functionName"])) {
                $value          = $this->data[$map["field"]];
                $functionName   = $map["functionName"];

                try {
                    $this->$functionName($value);
                    $this->mappers[$mapKey]["value"] = $value;
                } catch (Exception $e) {
                    if ($map["isRequired"]) {
                        $this->mappers[$mapKey]["errorMessage"] = $e->getMessage();
                    }
                }
            }
        }
    }

    public function isValid()
    {
        $isValid = true;
        foreach ($this->mappers as $map) {
            if (!is_null($map["errorMessage"])) {
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    public function getValue(string $field): mixed
    {
        return $this->data[$field];
    }

    public function getErrorMessages()
    {
        $message = "";
        foreach ($this->mappers as $map) {
            if (!is_null($map["errorMessage"])) {
                $error = $map["errorMessage"];
                $message .= "$error\n";
            }
        }
        return $message;
    }

    protected function isCurrencyAvailable($value): bool
    {
        if (!in_array($value, Currency::$availableCurrencies)) {
            throw new Exception("Currency '$value' is not available");
        }
        return true;
    }
}
