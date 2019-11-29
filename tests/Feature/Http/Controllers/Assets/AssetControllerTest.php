<?php

namespace Tests\Feature\Http\Controllers\Assets;

use Mockery;
use Tests\Feature\FeatureCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use GetCandy\Api\Core\Assets\Models\Asset;
use GetCandy\Api\Core\Products\Models\Product;
use GetCandy\Api\Core\Assets\Services\AssetService;
use GetCandy\Api\Core\Assets\Jobs\CleanUpAssetFiles;
use GetCandy\Api\Http\Resources\Assets\AssetResource;

/**
 * @group feature
 */
class AssetControllerTest extends FeatureCase
{

    public function test_can_create_asset_from_file()
    {
        $user = $this->admin();
        $product = Product::first();
        Storage::fake('assets');

        $this->app->instance(AssetService::class, Mockery::mock(AssetService::class, function ($mock) {
            $mock->shouldReceive('upload')->once()->andReturn(Asset::first());
        }));

        config()->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => '/',
            'url' => '/',
            'visibility' => 'public',
        ]);

        $response = $this->actingAs($user)->json('POST', 'assets', [
            'file'      => UploadedFile::fake()->image('asset.jpg')->size(1999),
            'parent'    => 'products',
            'parent_id' => $product->encodedId()
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'kind',
                'external',
                'position',
                'primary',
                'url',
                'sub_kind',
                'original_filename',
                'size',
                'width',
                'height'
            ]
        ]);

        $response->assertJson([
            'data' => [
                'title'    => 'asset title',
                'caption'  => 'asset caption',
                'kind'     => 'image',
                'sub_kind' => 'jpeg',
                'size'     => '1999',
                'width'    => '200',
                'height'   => '200'
            ]
        ]);
    }

    // public function test_can_create_asset_from_url()
    // {
    //     $product = Product::first();

    //     $this->app->instance(AssetService::class, Mockery::mock(AssetService::class, function ($mock) {
    //         $mock->shouldReceive('upload')->once()->andReturn(Asset::first());
    //     }));

    //     $response = $this->actingAs($this->admin())->json('POST', 'assets', [
    //         'url'       => 'https://www.youtube.com/embed/C0DPdy98e4c',
    //         'mime_type' => 'youtube',
    //         'parent'    => 'products',
    //         'parent_id' => $product->encodedId()
    //     ]);

    //     dd($response->getContent());

    //     $response->assertJsonStructure([
    //         'data' => [
    //             'id',
    //             'title',
    //             'kind',
    //             'external',
    //             'position',
    //             'primary',
    //             'url',
    //             'sub_kind',
    //             'original_filename',
    //             'size',
    //             'width',
    //             'height'
    //         ]
    //     ]);

    //     $response->assertJson([
    //         'data' => []
    //     ]);
    // }

    public function test_can_update_asset()
    {
        $user = $this->admin();
        $asset = Asset::first();
        config()->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => '/',
            'url' => '/',
            'visibility' => 'public',
        ]);

        $response = $this->actingAs($user)->json('PUT', 'assets', [
            'assets' => [
                [
                    'id'      => $asset->encodedId(),
                    'caption' => 'new asset caption',
                    'title'   => 'new asset title'
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'caption' => 'new asset caption',
                    'title'   => 'new asset title'
                ]
            ]
        ]);
    }

    public function test_can_delete_asset()
    {
        $user = $this->admin();
        $asset = Asset::first();

        $this->expectsJobs(CleanUpAssetFiles::class);

        $response = $this->actingAs($user)->json('DELETE', "assets/{$asset->encodedId()}");
        $response->assertStatus(204);

        $response = $this->actingAs($user)->json('DELETE', "assets/{$asset->encodedId()}");
        $response->assertStatus(404);
    }

}
