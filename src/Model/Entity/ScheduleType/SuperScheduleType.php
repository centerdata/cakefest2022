<?php
declare(strict_types=1);

namespace App\Model\Entity\ScheduleType;

use DateTime;
use App\Model\Entity\ScheduleType;

class SuperScheduleType extends ScheduleType implements ScheduleTypeInterface
{
    public int $id = 1;
    public string $delimiter = "\t";
    public string $extension = 'txt';
    public string $label = 'Super Schedule';
    protected array $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    /**
     * @return bool
     */
    public function isScheduleType(): bool
    {
        $tokens = $this->firstTokens();
        return !is_null($tokens) && sizeof($tokens) == 4 && is_numeric($tokens[0]) && date_create_from_format('H:i', $tokens[1]);
    }

    /**
     * @param array $tokens
     * @return DateTime|null
     */
    public function formatDate(array $tokens): ?DateTime
    {
        $time = date_create_from_format('H:i', $tokens[1]);
        if (sizeof($tokens) == 4 && is_numeric($tokens[0]) && $time && key_exists($tokens[0], $this->days)) {
            return date_create_from_format('l H:i', $this->days[$tokens[0]] . ' ' . $time->format('H:i'));
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
        if (sizeof($tokens) == 4 && is_numeric($tokens[0])) {
            $start = date_create_from_format('H:i', $tokens[1]);
            $end = date_create_from_format('H:i', $tokens[2]);
            return [
                'dow' => $tokens[0] + 1,
                'appointment' => $tokens[3],
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i')
            ];
        }
        return null;
    }
}
