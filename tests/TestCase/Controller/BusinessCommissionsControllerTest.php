<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CustomerCommissionsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\CustomerCommissionsController Test Case
 */
class CustomerCommissionsControllerTest extends IntegrationTestCase
{

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
