<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\SchedulesController;
use App\Model\Entity\Schedule;
use App\Model\Table\SchedulesTable;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SchedulesController Test Case
 *
 * @uses \App\Controller\SchedulesController
 */
class SchedulesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $config = $this->getTableLocator()->exists('Schedules') ? [] : ['className' => SchedulesTable::class];
        $this->Schedules = $this->getTableLocator()->get('Schedules', $config);
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
        $this->post(Router::pathUrl('Schedules::add'), $postData);

        /**
         * @var Schedule $schedule
         */
        $schedule = $this->Schedules->findByFileName($pathinfo['basename'])->contain('TimeSlots')->first();
        $uploaded = Configure::read('App.paths.schedules') . DS . $schedule->checksum;
        $this->assertTrue(is_file($uploaded));
        unlink($uploaded); // // Clean up

        // Assert
        $this->assertResponseCode(302);
        $this->assertEquals($expectedResult, count($schedule->time_slots));
    }

    /**
     * Use same dataProvider as ScheduleControllerTest
     *
     * @return array
     */
    public function provideSchedules(): array
    {
        return require __DIR__ . DS . '../../data/dataProvider.php';
    }
}
