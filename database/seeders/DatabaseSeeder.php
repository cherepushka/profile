<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Заполнение базы данных
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ManagerSeeder::class,
        ]);
    }
}
