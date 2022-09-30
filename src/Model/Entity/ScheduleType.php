<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Core\Configure;
use DateTime;
use App\Model\Table\SchedulesTable;
use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Laminas\Diactoros\Exception\UploadedFileErrorException;
use Psr\Http\Message\UploadedFileInterface;

class ScheduleType
{
    use InstanceConfigTrait;
    use LocatorAwareTrait;

    protected $_defaultConfig = [
        'table' => 'Schedules',
        'extension' => null,
        'file' => null,
        'upload' => null,
        'path' => null,
        'checksum' => null,
        'destination' => null,
    ];

    protected SchedulesTable $Schedules;
    protected UploadedFileInterface $file;

    public int $id;
    public string $delimiter;
    public string $extension;
    public string $checksum;
    public string $label;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->Schedules = $this->fetchTable($this->getConfig('table'));

        $file = $this->getConfig('file');
        if ($file instanceof UploadedFileInterface) {
            $this->setUploadedFile($file);
        }
    }

    public function setUploadedFile(UploadedFileInterface $file): void
    {
        $ext = strtolower(substr($file->getClientFilename(), -3));
        $this->setConfig('extension', $ext);

        // Add path to config, if UploadedFile has the right extension
        if ($this->extension = $ext) {
            $this->file = $file;
            $this->setConfig('path', $file->getStream()->getMetadata('uri'));
            $this->checksum = sha1_file($this->getConfig('path'));

            $uploaddir = Configure::read('App.paths.schedules');
            if (!$uploaddir || !is_dir($uploaddir)) {
                $uploaddir = TMP;
            }
            $this->setConfig('destination', $uploaddir . DS . $this->checksum);
        }
    }

    /**
     * @return array|null
     */
    public function firstTokens(): ?array
    {
        $tokens = null;
        $path = $this->getConfig('path');

        if (!is_null($path)) {
            $fp = fopen($path, 'r');
            $tokens = $this->getTokens(fgets($fp));
            fclose($fp);
        }

        return $tokens;
    }

    /**
     * @param $line
     * @return array
     */
    public function getTokens($line): array
    {
        return explode($this->delimiter, $line);
    }

    public function hasCompleteWeek(): bool
    {
        $path = $this->getConfig('path');
        $week = [];

        if (!is_file($path)) {
            return false;
        }

        $fp = fopen($path, 'r');

        while (!feof($fp)) {
            $line = fgets($fp);
            if (!$line) {
                continue;
            }
            $date = $this->formatDate($this->getTokens($line));

            if ($date instanceof DateTime) {
                $week[$date->format('N')] = true;
            }

        }
        fclose($fp);

        return array_key_exists(1, $week) && array_key_exists(2, $week) && array_key_exists(3, $week)
            && array_key_exists(4, $week) && array_key_exists(5, $week);
    }

    public function extractTimeSlots(Schedule $schedule, UploadedFileInterface $uploadedFile): void
    {
        if ($this->Schedules->TimeSlots->find()->where(['schedule_id' => $schedule->id])->count()) {
            return;
        }

        $this->setUploadedFile($uploadedFile);
        $path = $this->getConfig('path');

        $fp = fopen($path, 'r');

        while (!feof($fp)) {
            $line = fgets($fp);
            if (!$line) {
                continue;
            }

            $timeSlot = $this->Schedules->TimeSlots->newEntity($this->extractLine($this->getTokens($line)));
            $timeSlot->schedule_id = $schedule->id;
            $this->Schedules->TimeSlots->save($timeSlot);
        }
        fclose($fp);

        $destination = $this->getConfig('destination');
        $uploadedFile->moveTo($destination);
        if (!is_file($destination)) {
            throw new UploadedFileErrorException('Unable to move file');
        }

        $schedule->file_location = $destination;
        $this->Schedules->save($schedule);
    }

    public function scheduleExists(): bool
    {
        return is_file($this->getConfig('destination'));
    }

    public function isScheduleType(): bool
    {
        return false;
    }

    /**
     * @param array $tokens
     * @return DateTime|null
     */
    public function formatDate(array $tokens): ?DateTime
    {
        return null;
    }

    public function extractLine(array $tokens): ?array
    {
        return null;
    }
}
