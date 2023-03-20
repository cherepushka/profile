<?php

namespace App\Http\Traits;

trait MapTrait
{
    public function replaceSpaces($value) {
        $search = [
            ' ', // Убираем пробелы
            '\u00a0' // Убираем неразрывные пробелы
        ];

        $value = json_decode(
            str_replace($search, '',  json_encode($value))
        );

        return $value;
    }

    public function map(array $validated)
    {
        $debugArray = [];
        $timeArray = ['contract_date'];

        foreach ($validated as $validKey => $validValue) {
            if (isset($this->validated_array[$validKey])) {
                $modelKey = $this->validated_array[$validKey];

                if (in_array($modelKey, $timeArray)) {
                    $this->$modelKey = date("Y-m-d H:i:s", strtotime($validValue));
                    $debugArray += [$modelKey => date("Y-m-d H:i:s", strtotime($validValue))];

                    continue;
                }

                $this->$modelKey = $validValue;
                $debugArray += [$modelKey => $validValue];
            }
        }

        return $this;
    }
}
