<?php
declare(strict_types=1);

namespace App\Model\Entity\ScheduleType;

use DateTime;

interface ScheduleTypeInterface
{
    /**
     * @return bool
     */
    public function isScheduleType(): bool;

    /**
     * @param array $tokens
     * @return DateTime|null
     */
    public function formatDate(array $tokens): ?DateTime;

    /**
     * @param array $tokens
     * @return array|null
     *
     */
    public function extractLine(array $tokens): ?array;

    /**
     * @return bool
     */
    public function scheduleExists(): bool;
}
