<?php

namespace App\Http\Traits;

trait MapTrait
{
    public function map(array $validated)
    {
        foreach ($this->validated_array as $data_key => $key_content) {
            if (isset($validated[$data_key])) {
                if ($data_key == 'InvoiceDate') {
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
