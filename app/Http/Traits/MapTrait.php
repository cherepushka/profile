<?php

namespace App\Http\Traits;

trait MapTrait
{
    public function replaceSpaces($value): mixed
    {
        $search = [
            ' ', // Убираем пробелы
            '\u00a0' // Убираем неразрывные пробелы
        ];

        return json_decode(
            str_replace($search, '', json_encode($value))
        );
    }

    public function map(array $validated): static
    {
        $timeArray = ['contract_date'];

        foreach ($validated as $validKey => $validValue) {
            if (isset($this->validated_array[$validKey])) {
                $modelKey = $this->validated_array[$validKey];

                if (in_array($modelKey, $timeArray)) {
                    $this->$modelKey = date("Y-m-d H:i:s", strtotime($validValue));

                    continue;
                }

                $this->$modelKey = $validValue;
            }
        }

        return $this;
    }
}
