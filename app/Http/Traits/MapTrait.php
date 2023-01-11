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
        $dateKeys = ["InvoiceDate", "contract_date", 'payment_date'];

        foreach ($this->validated_array as $data_key => $key_content) {
            if (isset($validated[$data_key])) {
                if (in_array($data_key, $dateKeys)) {
                    $date = strtotime($validated[$data_key]);
                    $this->$key_content = date('Y-m-d H:i:s', $date);

                } else {
                    $this->$key_content = $validated[$data_key];
                }
            } else {
                $this->$key_content = '';
            }
        }

        return $this;
    }
}
