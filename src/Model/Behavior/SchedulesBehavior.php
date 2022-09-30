<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use App\Model\Entity\Schedule;
use ArrayObject;
use App\Model\Factories\ScheduleTypeFactory;
use App\Model\Entity\ScheduleType\ScheduleTypeInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Schedules behavior
 */
class SchedulesBehavior extends Behavior
{
    protected UploadedFileInterface $uploadedFile;

    /**
     * Set value for schedule_type_id
     *
     * @param \Cake\Event\EventInterface $event
     * @param \ArrayObject $data
     * @param \ArrayObject $options
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        /**
         * @var UploadedFileInterface|null $file
         * @var ScheduleTypeInterface|null $scheduleType
         */
        $file = null;
        if ($data->offsetExists('file_name')) {
            $file = $data->offsetGet('file_name');
        }

        if (!$data->offsetExists('schedule_type_id')) {
            $data->offsetSet('schedule_type_id', 0);

            if ($file instanceof UploadedFileInterface) {
                $scheduleType = ScheduleTypeFactory::findByFile($file);
                if ($scheduleType instanceof ScheduleTypeInterface) {
                    $data->offsetSet('schedule_type', $scheduleType);
                    $data->offsetSet('schedule_type_id', $scheduleType->id);
                    $data->offsetSet('file_location', $file->getStream()->getMetadata('uri'));
                    $data->offsetSet('file_name', $file->getClientFilename());
                    $data->offsetSet('size', $file->getSize());
                    $data->offsetSet('checksum', $scheduleType->checksum);
                    $this->uploadedFile = $file;
                }
            }
        }
    }

    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        /**
         * @var Schedule $entity
         */
        $entity->schedule_type->extractTimeSlots($entity, $this->uploadedFile);
        $this->table()->loadInto($entity, ['TimeSlots']);
    }
}
