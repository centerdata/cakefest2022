<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LegacySchedulesTable;
use App\Model\Table\SchedulesTable;
use Cake\TestSuite\TestCase;

/**
 * Use Cupcake Ipsum files to test Table
 *
 * App\Model\Table\SchedulesTable Test Case
 * @property LegacySchedulesTable $Schedules
 */
class LegacySchedulesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SchedulesTable
     */
    protected $Schedules;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('LegacySchedules') ? [] : ['className' => LegacySchedulesTable::class];
        $this->Schedules = $this->getTableLocator()->get('LegacySchedules', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Schedules);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SchedulesTable::validationDefault()
     */
    public function testImportSchedules(): void
    {
        debug($this->Schedules::CALENDAR_EXPERT);

        $this->assertTrue(true);
    }
}
