<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomerEmailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomerEmailsTable Test Case
 */
class CustomerEmailsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomerEmailsTable
     */
    public $CustomerEmails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.customer_emails',
        'app.customers',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.country_codes',
        'app.customere_mobiles',
        'app.business_statuses',
        'app.businesses',
        'app.events',
        'app.event_types',
        'app.campaigns',
        'app.campaign_records',
        'app.customer_mobiles',
        'app.customer_commissions',
        'app.developers',
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
        $config = TableRegistry::exists('CustomerEmails') ? [] : ['className' => 'App\Model\Table\CustomerEmailsTable'];
        $this->CustomerEmails = TableRegistry::get('CustomerEmails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerEmails);

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
