<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TimeSlots Model
 *
 * @property \App\Model\Table\SchedulesTable&\Cake\ORM\Association\BelongsTo $Schedules
 *
 * @method \App\Model\Entity\TimeSlot newEmptyEntity()
 * @method \App\Model\Entity\TimeSlot newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TimeSlot[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TimeSlot get($primaryKey, $options = [])
 * @method \App\Model\Entity\TimeSlot findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TimeSlot patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TimeSlot[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TimeSlot|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TimeSlot saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TimeSlot[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeSlot[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeSlot[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeSlot[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TimeSlotsTable extends Table
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

        $this->setTable('time_slots');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
            'joinType' => 'INNER',
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
            ->integer('schedule_id')
            ->notEmptyString('schedule_id');

        $validator
            ->integer('dow')
            ->requirePresence('dow', 'create')
            ->notEmptyString('dow');

        $validator
            ->scalar('appointment')
            ->maxLength('appointment', 255)
            ->requirePresence('appointment', 'create')
            ->notEmptyString('appointment');

        $validator
            ->time('start')
            ->requirePresence('start', 'create')
            ->notEmptyTime('start');

        $validator
            ->time('end')
            ->requirePresence('end', 'create')
            ->notEmptyTime('end');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('schedule_id', 'Schedules'), ['errorField' => 'schedule_id']);

        return $rules;
    }
}
