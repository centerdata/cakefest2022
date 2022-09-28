<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SchedulesFixture
 */
class SchedulesFixture extends TestFixture
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
                'schedule_type_id' => 1,
                'file_name' => 'Lorem ipsum dolor sit amet',
                'file_location' => 'Lorem ipsum dolor sit amet',
                'checksum' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-09-26 13:28:30',
                'modified' => '2022-09-26 13:28:30',
            ],
        ];
        parent::init();
    }
}
