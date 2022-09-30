<?php
declare(strict_types=1);

namespace App\Model\Entity\ScheduleType;

use DateTime;
use DateInterval;
use App\Model\Entity\ScheduleType;

class TimetableMasterType extends ScheduleType implements ScheduleTypeInterface
{
    public int $id = 2;
    public string $delimiter = ',';
    public string $extension = 'csv';
    public string $label = 'Timetable Master';

    /**
     * @return bool
     */
    public function isScheduleType(): bool
    {
        $tokens = $this->firstTokens();
        return !is_null($tokens) && sizeof($tokens) == 4 && is_numeric($tokens[2]) && date_create_from_format('d-m-Y', $tokens[0]);
    }

    /**
     * @param array $tokens
     * @return DateTime|null
     */
    public function formatDate(array $tokens): ?DateTime
    {
        if (sizeof($tokens) == 4) {
            return date_create_from_format('d-m-Y', $tokens[0]);
        }
        return null;
    }

    /**
     * @param array $tokens
     * @return array|null
     *
     */
    public function extractLine(array $tokens): ?array
    {
        if (sizeof($tokens) == 4) {
            $date = date_create_from_format('d-m-Y', $tokens[0]);
            $start = date_create_from_format('H', $tokens[1]);
            $end = date_create_from_format('H', $tokens[1]);
            $end = $end->add(new DateInterval('PT' . $tokens[2] . 'M'));

            return [
                'dow' => $date->format('N'),
                'appointment' => $tokens[3],
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i')
            ];
        }
        return null;
    }
}
