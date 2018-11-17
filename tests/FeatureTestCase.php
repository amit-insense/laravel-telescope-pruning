<?php

namespace Insense\LaravelTelescopePruning\Tests;

use Illuminate\Queue\Queue;
use Laravel\Telescope\Telescope;
use Orchestra\Testbench\TestCase;
use Insense\LaravelTelescopePruning\Models\EntryModel;
use Illuminate\Foundation\Testing\TestResponse;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Storage\DatabaseEntriesRepository;
use Insense\LaravelTelescopePruning\TelescopePruningServiceProvider;

class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->loadFactoriesUsing($this->app, __DIR__. "/Storage/factories/");
        TestResponse::macro('terminateTelescope', [$this, 'terminateTelescope']);
        
        Telescope::flushEntries();
        
    }

    protected function tearDown()
    {
        Telescope::flushEntries();

        Queue::createPayloadUsing(null);

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            TelescopeServiceProvider::class,
            TelescopePruningServiceProvider::class
        ];
    }

    protected function resolveApplicationCore($app)
    {
        parent::resolveApplicationCore($app);

        $app->detectEnvironment(function () {
            return 'self-testing';
        });
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('logging.default', 'errorlog');

        $config->set('database.default', 'testbench');

        $config->set('telescope.storage.database.connection', 'testbench');

        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->when(DatabaseEntriesRepository::class)
            ->needs('$connection')
            ->give('testbench');
    }

    protected function loadTelescopeEntries()
    {
        $this->terminateTelescope();

        return EntryModel::all();
    }

    public function terminateTelescope()
    {
        Telescope::store(app(EntriesRepository::class));
    }
}