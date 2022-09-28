<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimeSlotsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimeSlotsTable Test Case
 */
class TimeSlotsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimeSlotsTable
     */
    protected $TimeSlots;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.TimeSlots',
        'app.Schedules',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('TimeSlots') ? [] : ['className' => TimeSlotsTable::class];
        $this->TimeSlots = $this->getTableLocator()->get('TimeSlots', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TimeSlots);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TimeSlotsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\TimeSlotsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
