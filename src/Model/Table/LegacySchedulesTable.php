<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\LegacySchedule;
use DateTime;
use DateInterval;
use Exception;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schedules Model
 *
 * @property \App\Model\Table\TimeSlotsTable&\Cake\ORM\Association\HasMany $TimeSlots
 *
 * @method \App\Model\Entity\Schedule newEmptyEntity()
 * @method \App\Model\Entity\Schedule newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Schedule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Schedule get($primaryKey, $options = [])
 * @method \App\Model\Entity\Schedule findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Schedule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Schedule[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Schedule|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedule saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LegacySchedulesTable extends Table
{
    const SUPER_SCHEDULE = 1;
    const TIMETABLE_MASTER = 2;
    const CALENDAR_EXPERT = 3;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('schedules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('TimeSlots', [
            'foreignKey' => 'schedule_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('schedule_type_id')
            ->requirePresence('schedule_type_id', 'create')
            ->notEmptyString('schedule_type_id');

        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 255)
            ->requirePresence('file_name', 'create')
            ->notEmptyFile('file_name');

        $validator
            ->scalar('file_location')
            ->maxLength('file_location', 255)
            ->requirePresence('file_location', 'create')
            ->notEmptyFile('file_location');

        $validator
            ->scalar('checksum')
            ->maxLength('checksum', 40)
            ->allowEmptyString('checksum');

        return $validator;
    }

    public function detectFormat($path, $name = ''): int
    {
        $result = 0;

        if (is_file($path)) {
            $pathinfo = pathinfo($name);

            $fp = fopen($path, 'r');
            $line = fgets($fp);
            switch (strtolower($pathinfo['extension'])) {
                case 'txt':
                    $tokens = explode("\t", $line);
                    if (sizeof($tokens) == 4 && is_numeric($tokens[0]) && date_create_from_format('H:i', $tokens[1])) {
                        $result = self::SUPER_SCHEDULE;
                    }

                    $tokens = explode(';', $line);
                    if (sizeof($tokens) == 3 && in_array($tokens[0], array('Ma', 'Di', 'Wo', 'Do', 'Vr'))) {
                        $result = self::CALENDAR_EXPERT;
                    }
                    break;
                case 'csv':
                    $tokens = explode(',', $line);
                    if (sizeof($tokens) == 4 && is_numeric($tokens[2]) && date_create_from_format('d-m-Y', $tokens[0])) {
                        $result = self::TIMETABLE_MASTER;
                    }
                    break;
                default:
                    $result = 0;

            }
            fclose($fp);
        }
        return $result;
    }

    public function checkDateContents($path, $schedule_type_id, $name = '')
    {
        $result = false;

        $fp = fopen($path, 'r');
        $found = array();
        if (is_file($path)) {
            $pathinfo = pathinfo($name);
            while (!feof($fp)) {
                $line = fgets($fp);

                if(!$line) {
                    continue;
                }

                switch ($schedule_type_id) {
                    case self::SUPER_SCHEDULE:
                        $tokens = explode("\t", $line);
                        if (sizeof($tokens) == 4 && is_numeric($tokens[0]) && $time = date_create_from_format('H:i', $tokens[1])) {
                            $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri');

                            if (key_exists($tokens[0], $days)) {
                                $date = date_create_from_format('l H:i', $days[$tokens[0]] . ' ' . $time->format('H:i'));
                            }
                        }
                        break;
                    case self::TIMETABLE_MASTER:
                        $tokens = explode(',', $line);
                        if (sizeof($tokens) == 4) {
                            $date = date_create_from_format('d-m-Y', $tokens[0]);
                        }
                        break;
                    case self::CALENDAR_EXPERT:
                        $tokens = explode(';', $line);
                        if (sizeof($tokens) == 3) {
                            $days = array('Ma' => 'Mon', 'Di' => 'Tue', 'Wo' => 'Wed', 'Do' => 'Thu', 'Vr' => 'Fri');
                            $date = date_create_from_format('l', $days[$tokens[0]]);
                        }
                        break;
                    default:
                        $date = '';
                        break;
                }

                if (isset($date) && !empty($date)) {
                    array_push($found, $date);
                }

            }
        }

        if ($this->checkCompleteWeek($found)) {
            $result = true;
        }
        fclose($fp);

        return $result;
    }

    /**
     * @param LegacySchedule $schedule
     * @return int|null
     * @throws Exception
     */
    public function extractTimeSlots(LegacySchedule $schedule): ?int
    {
        $alreadyloaded = $this->TimeSlots->find()->where(['schedule_id' => $schedule->id]);

        if ($alreadyloaded->count() > 1) {
            throw new Exception('File already extracted');
        }

        if ($schedule->schedule_type_id == 0 || empty($schedule->schedule_type_id)) {
            throw new Exception('Unknown schedule type');
        }

        $fp = @fopen($schedule->file_location, 'r');
        if ($fp == null) {
            throw new Exception('Unable to open file');
        }

        // format error -> we check certain fields to see if they have the expected content (like numeric or so)
        $format_error = 0;
        // counts how much lines we have actually saved
        $count_saved = 0;

        $counter = 0;
        set_time_limit(3600);

        while ($line = fgets($fp)) {

            if(!$line) {
                continue;
            }

            switch ($schedule->schedule_type_id) {
                case self::SUPER_SCHEDULE:
                    $tokens = explode("\t", $line);
                    if (sizeof($tokens) == 4 && is_numeric($tokens[0])) {
                        $start = date_create_from_format('H:i', $tokens[1]);
                        $end = date_create_from_format('H:i', $tokens[2]);
                        $data = array(
                            'schedule_id' => $schedule->id,
                            'dow' => $tokens[0] + 1,
                            'appointment' => $tokens[3],
                            'start' => $start->format('H:i'),
                            'end' => $end->format('H:i')
                        );
                    }
                    break;

                case self::TIMETABLE_MASTER:
                    $tokens = explode(',', $line);
                    if (sizeof($tokens) == 4) {
                        $date = date_create_from_format('d-m-Y', $tokens[0]);
                        $start = date_create_from_format('H', $tokens[1]);
                        $end = date_create_from_format('H', $tokens[1]);
                        $end = $end->add(new DateInterval('PT' . $tokens[2] . 'M'));
                        $data = array(
                            'schedule_id' => $schedule->id,
                            'dow' => $date->format('N'),
                            'appointment' => $tokens[3],
                            'start' => $start->format('H:i'),
                            'end' => $end->format('H:i')
                        );
                    }
                    break;

                case self::CALENDAR_EXPERT:
                    $tokens = explode(';', $line);
                    if (sizeof($tokens) == 3) {
                        $days = array('Ma', 'Di', 'Wo', 'Do', 'Vr');
                        $dow = array_search($tokens[0], $days) + 1;

                        $data = array(
                            'schedule_id' => $schedule->id,
                            'dow' => $dow,
                            'appointment' => str_replace('"', '', $tokens[1]),
                            'start' => str_pad(strval($tokens[2] + 8), 2, '0',STR_PAD_LEFT) . ':00',
                            'end' => str_pad(strval($tokens[2] + 9), 2, '0') . ':00',
                        );
                    }
                    break;

                default:
                    $data = array();
                    break;

            } // end switch

            if (sizeof($data) > 0) {
                $timeSlot = $this->TimeSlots->newEntity($data);
                if ($this->TimeSlots->save($timeSlot)) {
                    $count_saved++;
                }
            }
            $counter++;
        }
        fclose($fp);

        return $count_saved;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkCompleteWeek(array $data): bool
    {
        $week = array();

        foreach ($data as $date) {
            if ($date) {
                $week[$date->format('N')] = true;
            }
        }

        if (
            array_key_exists(1, $week) &&
            array_key_exists(2, $week) &&
            array_key_exists(3, $week) &&
            array_key_exists(4, $week) &&
            array_key_exists(5, $week)) {
            return true;
        }
        return false;
    }
}
