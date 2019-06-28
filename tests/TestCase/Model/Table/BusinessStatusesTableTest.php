<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BusinessStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BusinessStatusesTable Test Case
 */
class BusinessStatusesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BusinessStatusesTable
     */
    public $BusinessStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.business_statuses',
        'app.customers',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.country_codes',
        'app.customere_mobiles',
        'app.customer_commissions',
        'app.developers',
        'app.campaigns',
        'app.campaign_records',
        'app.customer_mobiles',
        'app.customer_images',
        'app.businesses',
        'app.customer_types'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('BusinessStatuses') ? [] : ['className' => 'App\Model\Table\BusinessStatusesTable'];
        $this->BusinessStatuses = TableRegistry::get('BusinessStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BusinessStatuses);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
