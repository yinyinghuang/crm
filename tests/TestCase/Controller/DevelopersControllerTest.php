<?php
namespace App\Test\TestCase\Controller;

use App\Controller\DevelopersController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\DevelopersController Test Case
 */
class DevelopersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.developers',
        'app.users',
        'app.departments',
        'app.roles',
        'app.privileges',
        'app.customer_commissions',
        'app.customers',
        'app.business_statuses',
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
