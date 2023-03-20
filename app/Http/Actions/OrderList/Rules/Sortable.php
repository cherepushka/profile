<?php

namespace App\Http\Actions\OrderList\Rules;

use App\Http\Actions\OrderList\Sorters\SortParser;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class Sortable implements Rule
{

    private string $errorMessage = 'Невалидное значение сортировки';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try{
            (new SortParser())->parse($value);
        } catch(Throwable $e){
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
