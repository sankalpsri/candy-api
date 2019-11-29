<?php

namespace Seeds;

use GetCandy\Api\Core\Assets\Models\Asset;
use Illuminate\Database\Seeder;

class AssetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Asset::create([
            'id'                => 1,
            'asset_source_id'   => 1,
            'position'          => 0,
            'location'          => 'products/AA',
            'assetable_id'      => 1,
            'assetable_type'    => 'GetCandy\Api\Products\Models\Product',
            'primary'           => 1,
            'kind'              => 'image',
            'sub_kind'          => 'jpeg',
            'width'             => 200,
            'height'            => 200,
            'title'             => 'asset title',
            'caption'           => 'asset caption',
            'original_filename' => 'asset.jpg',
            'size'              => 1999,
            'external'          => 0,
            'filename'          => 'EOF39f5p47BPFClTDIzMsfR3mG2y00TGaCBgoHF5.JPG',
        ]);
    }
}
