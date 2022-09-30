<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Schedules Controller
 *
 * @property \App\Model\Table\SchedulesTable $Schedules
 * @method \App\Model\Entity\Schedule[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SchedulesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->Schedules = $this->fetchTable('Schedules');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $schedule = $this->Schedules->newEmptyEntity();

        $this->viewBuilder()->setTemplate('form');

        if ($this->request->is('post')) {
            $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());
            if ($this->Schedules->save($schedule)) {
                $this->Flash->success(__('Schedule upload complete, ' . count($schedule->time_slots) . ' timeslots extracted'));
                return $this->redirect(array('action' => 'add'));
            } else {
                $message = [];
                foreach ($schedule->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        array_push($message, __($field) . ': ' . __($error));
                    }
                }
                $this->Flash->error(implode('<br>', $message), ['escape' => false]);
            }
        }

        $this->set('schedule', $schedule);
    }
}
