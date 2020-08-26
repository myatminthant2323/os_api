<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Item;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {

	$image = $faker->image('public/backendtemplate/itemimg', 200, 150, 'sports');
	$filepath = substr($image, 7);  
		return [
    	'codeno' => $faker->date('Ymd', 'now') . '-' . $faker->time('His', 'now'),
    	'name' => $faker->country,
    	'photo' => $filepath,
    	'price' => $faker->numberBetween(1000, 50000),
    	'discount' => $faker->numberBetween(0, 50),
    	'description' => $faker->text(200),
    	'subcategory_id' => factory(App\Subcategory::class),
    	'brand_id' => factory(App\Brand::class)
    ];
});