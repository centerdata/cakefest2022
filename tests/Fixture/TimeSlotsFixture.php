<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TimeSlotsFixture
 */
class TimeSlotsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'schedule_id' => 1,
                'dow' => 1,
                'appointment' => 'Lorem ipsum dolor sit amet',
                'start' => '13:28:58',
                'end' => '13:28:58',
                'created' => '2022-09-26 13:28:58',
                'modified' => '2022-09-26 13:28:58',
            ],
        ];
        parent::init();
    }
}
