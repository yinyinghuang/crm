<?php
namespace App\Test\TestCase\Controller;

use App\Controller\BusinessesController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\BusinessesController Test Case
 */
class BusinessesControllerTest extends IntegrationTestCase
{

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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
