<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BusinessesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BusinessesTable Test Case
 */
class BusinessesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BusinessesTable
     */
    public $Businesses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.businesses',
        'app.events',
        'app.event_types',
        'app.customers',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.country_codes',
        'app.customere_mobiles',
        'app.customer_commissions',
        'app.business_statuses',
        'app.developers',
        'app.campaigns',
        'app.campaign_records',
        'app.customer_mobiles',
        'app.customer_images'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Businesses') ? [] : ['className' => 'App\Model\Table\BusinessesTable'];
        $this->Businesses = TableRegistry::get('Businesses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Businesses);

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
