<?php

namespace Seeds;

use GetCandy\Api\Core\Assets\Models\AssetSource;
use Illuminate\Database\Seeder;

class AssetSourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssetSource::create([
            'id'      => 1,
            'name'    => 'Product images',
            'handle'  => 'products',
            'disk'    => 'public',
            'default' => 0,
            'path'    => 'products',
        ]);

        AssetSource::create([
            'id'      => 2,
            'name'    => 'Channel images',
            'handle'  => 'channels',
            'disk'    => 'public',
            'default' => 0,
            'path'    => null,
        ]);
    }
}
