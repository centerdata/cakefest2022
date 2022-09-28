<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateTimeSlots extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('time_slots')
            ->addColumn('schedule_id', 'integer', [
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('dow', 'integer', [
                'limit' => null,
                'null' => false,
                'comment' => 'Day Of Week, DateTimeInterface::format(\'n\')'
            ])
            ->addColumn('appointment', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('start', 'time', [
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('end', 'time', [
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(['schedule_id'])
            ->create();
    }
}
