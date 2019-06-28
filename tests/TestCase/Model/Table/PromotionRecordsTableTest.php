<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CampaignRecordsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CampaignRecordsTable Test Case
 */
class CampaignRecordsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CampaignRecordsTable
     */
    public $CampaignRecords;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.campaign_records',
        'app.campaigns',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.customer_commissions',
        'app.customers',
        'app.developers',
        'app.business_statuses'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CampaignRecords') ? [] : ['className' => 'App\Model\Table\CampaignRecordsTable'];
        $this->CampaignRecords = TableRegistry::get('CampaignRecords', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CampaignRecords);

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
