<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Schedules Controller
 *
 * @property \App\Model\Table\LegacySchedulesTable $Schedules
 * @method \App\Model\Entity\LegacySchedule[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LegacySchedulesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->Schedules = $this->fetchTable('LegacySchedules');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $schedule = $this->Schedules->newEmptyEntity();
        $this->set('schedule', $schedule);

        $this->viewBuilder()->setTemplatePath('Schedules');
        $this->viewBuilder()->setTemplate('form');

        if ($this->request->is('post')) {

            /**
             * @var UploadedFileInterface $upload
             */
            $upload = $this->request->getData('file_name');

            // Check whether there is a file uploaded
            if ($upload->getError() == UPLOAD_ERR_NO_FILE) {
                $this->Flash->error(__('Failed to upload'));
                return;
            }

            // type needs to be set
            $tmp_name = $upload->getStream()->getMetadata()['uri'];
            $schedule_type_id = $this->Schedules->detectFormat(
                $tmp_name,
                $upload->getClientFilename()
            );

            if ($schedule_type_id == 0) {
                $this->Flash->error(__('Unsupported schedule file'));
                return;
            }

            if (!$this->Schedules->checkDateContents($tmp_name, $schedule_type_id, $upload->getClientFilename())) {
                $this->Flash->error(__('The schedule does not contain a complete week'));
                return;
            }

            // Create schedule
            $uploaddir = Configure::read('App.paths.schedules');

            if (!$uploaddir || !is_dir($uploaddir)) {
                $uploaddir = TMP;
            }

            $filesize = $upload->getSize();
            $checksum = sha1_file($tmp_name);
            $destination = $uploaddir . DS . $checksum;

            if (is_file($destination)) {
                $this->Flash->error(__('This schedule was already uploaded'));
                return;
            }

            $upload->moveTo($destination);
            if (!is_file($destination)) {
                $this->Flash->error(__('Failed to write file to disk'));
                return;
            } else {
                $schedule = $this->Schedules->newEntity([
                    'schedule_type_id' => $schedule_type_id,
                    'file_name' => $upload->getClientFilename(),
                    'file_location' => $destination,
                    'size' => $filesize,
                    'checksum' => $checksum,
                ]);
            }

            if ($this->Schedules->save($schedule)) {
                $timeSlots = $this->Schedules->extractTimeSlots($schedule);
                $this->Flash->success(__('Schedule upload complete, ' . $timeSlots . ' timeslots extracted'));

                return $this->redirect(array('action' => 'add'));
            } else {
                $this->set('errors', $schedule->getErrors());
                $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
            }
        }
    }
}
