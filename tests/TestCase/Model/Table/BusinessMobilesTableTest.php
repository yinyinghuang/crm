<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomerMobilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomerMobilesTable Test Case
 */
class CustomerMobilesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomerMobilesTable
     */
    public $CustomerMobiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.customer_mobiles',
        'app.customers',
        'app.country_codes',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.customer_commissions',
        'app.business_statuses',
        'app.developers',
        'app.campaigns',
        'app.campaign_records'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CustomerMobiles') ? [] : ['className' => 'App\Model\Table\CustomerMobilesTable'];
        $this->CustomerMobiles = TableRegistry::get('CustomerMobiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerMobiles);

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
