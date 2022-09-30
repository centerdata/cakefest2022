<?php
declare(strict_types=1);

namespace App\Model\Entity\ScheduleType;

use DateTime;
use App\Model\Entity\ScheduleType;

class CalendarExpertType extends ScheduleType implements ScheduleTypeInterface
{
    public int $id = 3;
    public string $delimiter = ';';
    public string $extension = 'txt';
    public string $label = 'Calendar Expert';
    protected array $days = ['Ma' => 'Mon', 'Di' => 'Tue', 'Wo' => 'Wed', 'Do' => 'Thu', 'Vr' => 'Fri'];

    /**
     * @return bool
     */
    public function isScheduleType(): bool
    {
        $tokens = $this->firstTokens();
        return !is_null($tokens) && sizeof($tokens) == 3 && in_array($tokens[0], array_keys($this->days));
    }

    /**
     * @param array $tokens
     * @return DateTime|null
     */
    public function formatDate(array $tokens): ?DateTime
    {
        if (sizeof($tokens) == 3) {
            return date_create_from_format('l', $this->days[$tokens[0]]);
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
        if (sizeof($tokens) == 3) {
            $dow = array_search($tokens[0], array_keys($this->days)) + 1;

            return [
                'dow' => $dow,
                'appointment' => str_replace('"', '', $tokens[1]),
                'start' => str_pad(strval($tokens[2] + 8), 2, '0', STR_PAD_LEFT) . ':00',
                'end' => str_pad(strval($tokens[2] + 9), 2, '0') . ':00',
            ];
        }
        return null;
    }
}
