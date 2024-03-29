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
            array(
                'name' => 'Кирилл',
                'surname' => 'Рыжов',
                'position' => 'Заместитель генерального директора',
                'email' => 'kir@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.103), (495)517-72-61, (495)517-02-61',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/kir.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Евгения',
                'surname' => 'Каманина',
                'position' => 'Менеджер по продажам',
                'email' => 'kam@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.123)',
                'whats_app' => '+7(926) 834-17-32',
                'image' => 'https://fluid-line.ru/signatures/images/kam.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Алексей',
                'surname' => 'Капитанов',
                'position' => 'Менеджер по продажам',
                'email' => 'kap@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.125)',
                'whats_app' => '+7(926) 267-46-52',
                'image' => 'https://fluid-line.ru/signatures/images/kap.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Алексей',
                'surname' => 'Замараев',
                'position' => 'Менеджер по продажам',
                'email' => 'zam@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.106)',
                'whats_app' => '+7(926) 932-86-50',
                'image' => 'https://fluid-line.ru/signatures/images/zam.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Иван',
                'surname' => 'Горбунов',
                'position' => 'Менеджер по продажам',
                'email' => 'ivg@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.113)',
                'whats_app' => '+7(926) 800-62-79',
                'image' => 'https://fluid-line.ru/signatures/images/ivg.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Алексей',
                'surname' => 'Калашников',
                'position' => 'Менеджер по продажам',
                'email' => 'kalashnikov@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.105)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/kalashnikov.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Лаура',
                'surname' => 'Бейбит',
                'position' => 'Помощник менеджера',
                'email' => 'fl2@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.120)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/fl2.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Илья',
                'surname' => 'Манойло',
                'position' => 'Менеджер по продажам',
                'email' => 'ism@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.124)',
                'whats_app' => '+7(926) 704-29-60',
                'image' => 'https://fluid-line.ru/signatures/images/ism.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Назар',
                'surname' => 'Лысиков',
                'position' => 'Помощник менеджера по продажам',
                'email' => 'lnm@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.134)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/assets/snippets/profile/new-profile-page/profile-images/lysikov.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Диана',
                'surname' => 'Демченко',
                'position' => 'Менеджер по продажам',
                'email' => 'ddk@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.102)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/ddk.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Анастасия',
                'surname' => 'Ткаченко',
                'position' => 'Менеджер по продажам',
                'email' => 'tav@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.133)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/tav.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Сухоруков',
                'surname' => 'Артем',
                'position' => 'Помощник менеджера',
                'email' => 'saa@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.134)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/saa.jpg',
                'status' => '0',
            ),
            array(
                'name' => 'Илья',
                'surname' => 'Кузнецов',
                'position' => 'Помощник менеджера',
                'email' => 'fl3@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.167)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/fl3.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Вячеслав',
                'surname' => 'Бондырев',
                'position' => 'Менеджер по продажам',
                'email' => 'bvv@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.144)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/bvv.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Кальмар',
                'surname' => 'Салманов',
                'position' => 'Помощник менеджера',
                'email' => 'sae@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.132)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/sae.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Евгений',
                'surname' => 'Войтов',
                'position' => 'Менеджер по продажам',
                'email' => 'vea@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.137)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/vea.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Дарья',
                'surname' => 'Савельева',
                'position' => 'Менеджер по продажам',
                'email' => 'ddn@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.135)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/ddn.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Терентьев',
                'surname' => 'Евгений',
                'position' => 'Инженер по качеству',
                'email' => 'quality@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.1)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/assets/snippets/profile/new-profile-page/profile-images/terent\'ev.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Мясников',
                'surname' => 'Максим',
                'position' => 'Помощник менеджера по продажам',
                'email' => 'fl1@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.170)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/fl1.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Кидинкин',
                'surname' => 'Денис',
                'position' => 'Менеджер по продажам',
                'email' => 'kda@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.136)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/kda.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Шенчук',
                'surname' => 'Юлианна',
                'position' => 'Менеджер по продажам',
                'email' => 'uas@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.141)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/uas.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Новикова',
                'surname' => 'Светлана',
                'position' => 'Менеджер по контролю качества обслуживания клиентов',
                'email' => 'nsu@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.131)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/signatures/images/nsu.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Стрельцова',
                'surname' => 'Юлия',
                'position' => 'Офис-менеджер',
                'email' => 'office2@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.146)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/assets/snippets/profile/new-profile-page/profile-images/strel\'zova.jpg',
                'status' => '1',
            ),
            array(
                'name' => 'Степан',
                'surname' => 'Сапрыкин',
                'position' => 'Помощник менеджера по продажам',
                'email' => 'sap@fluid-line.ru',
                'phone' => '+7(495) 984-41-01 (доб.166)',
                'whats_app' => '',
                'image' => 'https://fluid-line.ru/assets/snippets/profile/new-profile-page/profile-images/sap.jpg',
                'status' => '1',
            ),
        ]);
    }
}
