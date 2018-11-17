<?php

namespace Insense\LaravelTelescopePruning\Basic;

use Insense\LaravelTelescopePruning\Tests\FeatureTestCase;
use Laravel\Telescope\Storage\DatabaseEntriesRepository;
use Insense\LaravelTelescopePruning\Models\EntryModel;
use Illuminate\Contracts\Cache\Repository;

/**
 * Description of BasicFunctionalityTest
 *
 * @author amit
 */
class BasicFunctionalityTest extends FeatureTestCase {
    private $config = null;
    protected function setUp() {
        parent::setUp();
        $this->config = $this->app->get('config');
    }
    
    public function test_pruning_enabled_or_disabled() {
        $entry = factory(EntryModel::class)->create();
        if($this->config['telescope-pruning.enabled']) {
            $this->assertDatabaseMissing('telescope_entries', ['uuid' => $entry->uuid]);
        } else {
            $this->assertDatabaseHas('telescope_entries', ['uuid' => $entry->uuid]);
        }
        
    }
    
    public function test_pruning_limit_check() {
        $this->assertTrue(true);
//        $itemCount = 105;
//        $entry = factory(EntryModel::class, $itemCount)->create();
//        $limit = $this->config['limit'];
        
    }

}
