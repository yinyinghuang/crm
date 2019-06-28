<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomerImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomerImagesTable Test Case
 */
class CustomerImagesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomerImagesTable
     */
    public $CustomerImages;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.customer_images',
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
        'app.customer_mobiles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CustomerImages') ? [] : ['className' => 'App\Model\Table\CustomerImagesTable'];
        $this->CustomerImages = TableRegistry::get('CustomerImages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerImages);

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