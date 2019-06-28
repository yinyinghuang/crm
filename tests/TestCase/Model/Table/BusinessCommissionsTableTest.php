<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomerCommissionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomerCommissionsTable Test Case
 */
class CustomerCommissionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomerCommissionsTable
     */
    public $CustomerCommissions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.customer_commissions',
        'app.customers',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.business_statuses',
        'app.developers',
        'app.campaigns'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CustomerCommissions') ? [] : ['className' => 'App\Model\Table\CustomerCommissionsTable'];
        $this->CustomerCommissions = TableRegistry::get('CustomerCommissions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerCommissions);

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
