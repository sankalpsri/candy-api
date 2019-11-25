<?php

namespace Tests\Feature\Http\Controllers\Assets;

use Tests\Feature\FeatureCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use GetCandy\Api\Core\Products\Models\Product;
use GetCandy\Api\Core\Assets\Models\Asset;
use Mockery;
use GetCandy\Api\Core\Assets\Services\AssetService;

/**
 * @group feature
 */
class AssetControllerTest extends FeatureCase
{

    public function test_can_create_asset_from_url()
    {
        $user = $this->admin();

        // Get an attribute group
        $product = Product::first();
        Storage::fake('assets');

        $this->app->instance(AssetService::class, Mockery::mock(AssetService::class, function ($mock) {
            $mock->shouldReceive('upload')->once()->andReturn(Asset::first());
        }));

        $response = $this->actingAs($user)->json('POST', 'assets', [
            'file'      => UploadedFile::fake()->image('asset.jpg')->size(1999),
            'parent'    => 'products',
            'parent_id' => $product->encodedId()
        ]);

        dd($response->content());

        $structure = [
            'data' => [
                'id',
                'name',
                'handle',
                'position',
            ],
            'meta'
        ];

        $response->assertJsonStructure($structure);

        $response->assertJson([
            'data' => [
                'name' => [
                    'en' => 'Test Group',
                ],
                'handle' => 'test-group',
            ]
        ]);
    }

    protected function getUploadResponse()
    {
        

        
    }

    public function test_can_update_attribute_group()
    {
        $user = $this->admin();

        // Get an attribute group
        $group = AttributeGroup::first();

        $response = $this->actingAs($user)->json('PUT', "attribute-groups/{$group->encodedId()}", [
            'name' => [
                'en' => 'Updated Name',
            ],
            'handle' => 'updated-handle',
            'position' => 9999,
        ]);

        $structure = [
            'data' => [
                'id',
                'name',
                'handle',
                'position',
            ],
            'meta'
        ];

        $response->assertJsonStructure($structure);

        $response->assertJson([
            'data' => [
                'name' => [
                    'en' => 'Updated Name',
                ],
                'handle' => 'updated-handle',
                'position' => 9999,
            ],
        ]);
    }

    /**
     * @group test
     */
    public function test_can_reorder_attribute_groups()
    {
        $user = $this->admin();

        $response = $this->actingAs($user)->json('GET', 'attribute-groups');

        $marketing = AttributeGroup::whereHandle('marketing')->first();
        $seo = AttributeGroup::whereHandle('seo')->first();


        $response->assertJson([
            'data' => [
                [
                    'handle' => 'marketing',
                    'position' => 1,
                ],
                [
                    'handle' => 'seo',
                    'position' => 2,
                ]
            ],
        ]);

        $this->actingAs($user)->json('PUT', 'attribute-groups/order', [
            'groups' => [
                $marketing->encodedId() => 2,
                $seo->encodedId() => 1,
            ]
        ])->assertStatus(204);

        $response = $this->actingAs($user)->json('GET', 'attribute-groups');

        $response->assertJson([
            'data' => [
                [
                    'handle' => 'seo',
                    'position' => 1,
                ],
                [
                    'handle' => 'marketing',
                    'position' => 2,
                ],
            ],
        ]);

    }

    public function test_can_delete_attribute_group()
    {
        $user = $this->admin();

        // Get an attribute group
        $group = AttributeGroup::first();

        $response = $this->actingAs($user)->json('DELETE', "attribute-groups/{$group->encodedId()}");

        $response->assertStatus(204);

        $response = $this->actingAs($user)->json('DELETE', "attribute-groups/{$group->encodedId()}");

        $response->assertStatus(404);
    }
}
