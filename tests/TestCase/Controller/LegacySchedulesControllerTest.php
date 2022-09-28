<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\SchedulesController;
use App\Model\Entity\LegacySchedule;
use App\Model\Table\LegacySchedulesTable;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SchedulesController Test Case
 *
 * @property LegacySchedulesTable $LegacySchedules
 * @uses \App\Controller\SchedulesController
 */
class LegacySchedulesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $config = $this->getTableLocator()->exists('LegacySchedules') ? [] : ['className' => LegacySchedulesTable::class];
        $this->LegacySchedules = $this->getTableLocator()->get('LegacySchedules', $config);
    }

    /**
     * Test add method
     *
     * @dataProvider provideSchedules
     * @return void
     * @uses         \App\Controller\SchedulesController::add()
     */
    public function testAdd($expectedResult, $file): void
    {
        // Arrange
        $pathinfo = pathinfo($file);
        #debug($pathinfo);

        $tmp = TMP . $pathinfo['basename'];
        copy($file, $tmp);

        $scheduleFile = new \Laminas\Diactoros\UploadedFile(
            $tmp,
            filesize($file),
            \UPLOAD_ERR_OK,
            $pathinfo['basename'],
            mime_content_type($file),
        );

        $postData = [
            'file_name' => $scheduleFile,
        ];
        $this->configRequest([
            'files' => $postData,
        ]);

        // Act
        $this->post(Router::pathUrl('LegacySchedules::add'), $postData);

        /**
         * @var LegacySchedule $schedule
         */
        $schedule = $this->LegacySchedules->findByFileName($pathinfo['basename'])->contain('TimeSlots')->first();
        $uploaded = Configure::read('App.paths.schedules') . DS . $schedule->checksum;
        $this->assertTrue(is_file($uploaded));
        unlink($uploaded); // // Clean up

        // Assert
        $this->assertResponseCode(302);
        $this->assertEquals($expectedResult, count($schedule->time_slots));
    }

    public function provideSchedules(): array
    {
        return [
            [20, __DIR__ . DS . '..' . DS . '..' . DS . 'data' . DS . 'Calendar_Expert.txt'],
            [9, __DIR__ . DS . '..' . DS . '..' . DS . 'data' . DS . 'SuperSchedule.txt'],
            [8, __DIR__ . DS . '..' . DS . '..' . DS . 'data' . DS . 'Timetable_Master.csv'],
        ];
    }
}
