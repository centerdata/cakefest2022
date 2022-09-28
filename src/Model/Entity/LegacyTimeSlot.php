<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TimeSlot Entity
 *
 * @property int $id
 * @property int $schedule_id
 * @property int $dow
 * @property string $appointment
 * @property \Cake\I18n\Time $start
 * @property \Cake\I18n\Time $end
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Schedule $schedule
 */
class LegacyTimeSlot extends Entity
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
        'schedule_id' => true,
        'dow' => true,
        'appointment' => true,
        'start' => true,
        'end' => true,
        'created' => true,
        'modified' => true,
        'schedule' => true,
    ];
}
