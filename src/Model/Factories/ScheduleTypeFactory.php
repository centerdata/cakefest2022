<?php
declare(strict_types=1);

namespace App\Model\Factories;

use Cake\Collection\Collection;
use App\Model\Entity\ScheduleType\ScheduleTypeInterface;
use App\Model\Entity\ScheduleType\CalendarExpertType;
use App\Model\Entity\ScheduleType\SuperScheduleType;
use App\Model\Entity\ScheduleType\TimetableMasterType;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @property array $scheduleTypes
 */
class ScheduleTypeFactory
{
    public static array $scheduleTypes = [
        CalendarExpertType::class,
        SuperScheduleType::class,
        TimetableMasterType::class
    ];

    /**
     * find correct type based on an upload file
     */
    public static function findByFile(?UploadedFileInterface $file): ?ScheduleTypeInterface
    {
        if (is_null($file)) {
            return null;
        }

        $scheduleTypes = self::find(['file' => $file]);

        /**
         * @var ScheduleTypeInterface $scheduleType
         */
        return $scheduleTypes->filter(function ($scheduleType) {
            return $scheduleType->isScheduleType();
        })->first();
    }

    /**
     * @param array $options
     * @return \Cake\Collection\Collection<ScheduleTypeInterface>
     */
    public static function find(array $options = []): Collection
    {
        $result = [];

        foreach (self::$scheduleTypes as $className) {
            array_push($result, new $className($options));
        }

        return new Collection($result);
    }

    /**
     * @param int $id
     * @param array $options
     * @return ScheduleTypeInterface|null
     */
    public static function get($id, array $options = []): ?ScheduleTypeInterface
    {
        /**
         * @var ScheduleTypeInterface $scheduleType
         */
        return self::find($options)->filter(function ($scheduleType) use ($id) {
            return $scheduleType->id == $id;
        })->first();
    }
}
