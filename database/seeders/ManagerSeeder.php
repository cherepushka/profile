<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manager')->insert([
            [
                'name' => 'Кирилл',
                'surname' => 'Рыжов',
                'position' => 'Заместитель генерального директора',
                'email' => 'kir@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.103), (495)517-72-61, (495)517-02-61',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/kir.jpg',
                'status' => '1',
            ], [
                'name' => 'Евгения',
                'surname' => 'Каманина',
                'position' => 'Менеджер по продажам',
                'email' => 'kam@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.123)',
                'whats_app' => '+7(926) 834-17-32',
                'image' => 'https://fluid-line.ru/signatures/images/kam.jpg',
                'status' => '1',
            ], [
                'name' => 'Алексей',
                'surname' => 'Капитанов',
                'position' => 'Менеджер по продажам',
                'email' => 'kap@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.125)',
                'whats_app' => '+7(926) 267-46-52',
                'image' => 'https://fluid-line.ru/signatures/images/kap.jpg',
                'status' => '1',
            ], [
                'name' => 'Алексей',
                'surname' => 'Замараев',
                'position' => 'Менеджер по продажам',
                'email' => 'zam@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.106)',
                'whats_app' => '+7(926) 267-46-52',
                'image' => 'https://fluid-line.ru/signatures/images/zam.jpg',
                'status' => '1',
            ], [
                'name' => 'Иван',
                'surname' => 'Горбунов',
                'position' => 'Менеджер по продажам',
                'email' => 'ivg@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.113)',
                'whats_app' => '+7(926) 800-62-79',
                'image' => 'https://fluid-line.ru/signatures/images/ivg.jpg',
                'status' => '1',
            ], [
                'name' => 'Алексей',
                'surname' => 'Калашников',
                'position' => 'Менеджер по продажам',
                'email' => 'kalashnikov@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.105)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/kalashnikov.jpg',
                'status' => '1',
            ], [
                    'name' => 'Назар',
                    'surname' => 'Лысиков',
                    'position' => 'Помощник менеджера по продажам',
                    'email' => 'lnm@fluid-line.ru',
                    'phone' => '+7(495) 984-41-01 (доб.134)',
                    'whats_app' => '',
                    'image' => 'https://fluid-line.ru//assets/snippets/profile/new-profile-page/profile-images/lysikov.jpg',
                    'status' => '1',
            ]
        ]);
    }
}
