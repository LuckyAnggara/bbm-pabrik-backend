<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\User;
use App\Models\ItemType;
use App\Models\ItemUnit;
use App\Models\Machine;
use App\Models\Overhead;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name' => 'asdasdasd',
            'username' => '123',
            'password' => '$2y$10$DoqfCFk9hG8GQGUvoeoOT.XOUSdM4UHAhcehpkDSkBtciH62UcUru',
        ]);

        // ItemType::factory()->create([
        //     'name' => 'barang baku',
        //     'created_by' => '1'
        // ]);

        // ItemType::factory()->create([
        //     'name' => 'barang jadi',
        //     'created_by' => '1'
        // ]);

        // ItemType::factory()->create([
        //     'name' => 'lainnya',
        //     'created_by' => '1'
        // ]);

        // ItemUnit::factory()->create([
        //     'name' => 'kilogram',
        //     'abbreviation' => 'kg',
        //     'created_by' => '1'
        // ]);

        // ItemUnit::factory()->create([
        //     'name' => 'meter',
        //     'abbreviation' => 'mtr',
        //     'created_by' => '1'
        // ]);

        // ItemUnit::factory()->create([
        //     'name' => 'buah',
        //     'abbreviation' => 'buah',
        //     'created_by' => '1'
        // ]);

        // ItemUnit::factory()->create([
        //     'name' => 'ton',
        //     'abbreviation' => 'ton',
        //     'created_by' => '1'
        // ]);

        // Warehouse::factory()->create([
        //     'name' => 'gudang pusat',
        //     'location' => 'limbangan jawa barat',
        //     'created_by' => '1'
        // ]);

        // Item::factory()->create([
        //     'name' => 'Plastik',
        //     'type_id' => '1',
        //     'unit_id' => '1',
        //     'warehouse_id' => '1',
        //     'created_by' => '1',
        // ]);

        // DB::table('machines')->insert([
        //     'name' => 'Roll Forming Machine Standard - 1',
        //     'usage_capacity' => '100000',
        // ]);
        
        // DB::table('overheads')->insert([
        //     'name' => 'Listrik',
        //     'unit' => 'Watt',
        // ]);
        // DB::table('overheads')->insert([
        //     'name' => 'Pegawai',
        //     'unit'=> 'Orang'
        // ]);
        // DB::table('overheads')->insert([
        //     'name' => 'Air',
        //     'unit'=> 'm3'
        // ]);
    }
}
