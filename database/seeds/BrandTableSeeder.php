<?php

use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        factory(App\Brand::class, 3)->create()->each(function ($brand) {
            $items = factory(App\Item::class, 1)->make();
            $brand->items()->saveMany($items);
          });
    }
}
