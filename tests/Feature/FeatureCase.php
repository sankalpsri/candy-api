<?php

namespace Tests\Feature;

use GetCandy;
use Tests\Stubs\User;
use Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class FeatureCase extends TestCase
{
    protected $userToken;

    protected $clientToken;

    protected $headers = [];

    public function setUp() : void
    {
        parent::setUp();
        GetCandy::routes();
        $this->artisan('key:generate');
        $this->artisan('passport:install');
        $this->artisan('vendor:publish', ['--provider' => 'Intervention\Image\ImageServiceProviderLaravelRecent']);


    }

    protected function getResponseContents($response)
    {
        return json_decode($response->content());
    }

    public function admin()
    {
        $user = User::first();
        $user->assignRole('admin');
        return $user;
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = $user->createToken('TestToken', [])->accessToken;

        $this->headers['Accept'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer ' . $token;

        return $this;
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('auth.guards.api', [
            'driver' => 'passport',
            'provider' => 'users',
        ]);

        // $app['config']->set('env', 'local');
        // $app['config']->set('debug', true);
        $app['config']->set('assets.max_filesize', '2000');
        $app['config']->set('assets.allowed_filetypes', 'jpg,jpeg,png,pdf,gif,bmp,svg,doc,docx,xls,csv');
        $app['config']->set('assets.upload_drivers', [
            'vimeo' => GetCandy\Api\Core\Assets\Drivers\Vimeo::class,
            'application' => GetCandy\Api\Core\Assets\Drivers\File::class,
            'youtube' => GetCandy\Api\Core\Assets\Drivers\YouTube::class,
            'image' => GetCandy\Api\Core\Assets\Drivers\Image::class,
            'external' => GetCandy\Api\Core\Assets\Drivers\ExternalImage::class,
        ]);
    }

    public function json($method, $uri, array $data = [], array $headers = [])
    {
        return parent::json($method, $uri, $data, array_merge($this->headers, $headers));
    }
}
