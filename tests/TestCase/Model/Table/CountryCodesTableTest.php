<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CountryCodesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CountryCodesTable Test Case
 */
class CountryCodesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CountryCodesTable
     */
    public $CountryCodes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.country_codes',
        'app.customers',
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
        $config = TableRegistry::exists('CountryCodes') ? [] : ['className' => 'App\Model\Table\CountryCodesTable'];
        $this->CountryCodes = TableRegistry::get('CountryCodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CountryCodes);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
