<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\ScheduleType\ScheduleTypeInterface;
use App\Model\Factories\ScheduleTypeFactory;
use Cake\ORM\Entity;

/**
 * Schedule Entity
 *
 * @property int $id
 * @property int $schedule_type_id
 * @property int $size
 * @property string $file_name
 * @property string $file_location
 * @property string|null $checksum
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\TimeSlot[] $time_slots
 * @property ScheduleTypeInterface $schedule_type
 */
class Schedule extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'schedule_type_id' => true,
        'size' => true,
        'file_name' => true,
        'file_location' => true,
        'checksum' => true,
        'created' => true,
        'modified' => true,
        'time_slots' => true,
        'schedule_type' => true,
    ];
    protected $_virtual = ['scheduleType'];

    protected function _getScheduleType(): ?ScheduleTypeInterface
    {
        return ScheduleTypeFactory::get($this->schedule_type_id, ['file' => $this->file_location]);
    }

}
