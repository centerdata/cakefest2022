<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateSchedules extends AbstractMigration
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
        $this->table('schedules')
            ->addColumn('schedule_type_id', 'integer', [
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('size', 'integer', [
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('file_name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_location', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('checksum', 'string', [
                'default' => null,
                'limit' => 40,
                'null' => true,
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
            ->addIndex(['schedule_type_id'])
            ->create();
    }
}
