<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\ScheduleType\ScheduleTypeInterface;
use Cake\ORM\Table;
use Cake\Utility\Hash;
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
class SchedulesTable extends Table
{
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

        $this->addBehavior('Schedules');
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
            ->setStopOnFailure()
            ->integer('schedule_type_id')
            ->requirePresence('schedule_type_id', 'create')
            ->notEmptyString('schedule_type_id')
            ->greaterThan('schedule_type_id', 0, __('Unsupported schedule file'))
            ->add('schedule_type_id', 'hasCompleteWeek', [
                'rule' => function ($value, $context) {
                    $scheduleType = Hash::get($context, 'data.schedule_type', null);
                    if (!$scheduleType instanceof ScheduleTypeInterface) {
                        return false;
                    }
                    return $scheduleType->hasCompleteWeek();
                },
                'message' => __('The schedule does not contain a complete week'),
            ]);
        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 255)
            ->requirePresence('file_name', 'create')
            ->notEmptyFile('file_name')
            ->add('file_name', 'scheduleExists', [
                'rule' => function ($value, $context) {
                    $scheduleType = Hash::get($context, 'data.schedule_type', null);
                    if (!$scheduleType instanceof ScheduleTypeInterface) {
                        return false;
                    }
                    return !$scheduleType->scheduleExists();
                },
                'message' => __('This schedule was already uploaded'),
            ]);

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

}
