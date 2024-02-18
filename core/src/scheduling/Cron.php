<?php

namespace app\core\src\scheduling;

use app\core\src\factories\CronjobFactory;
use app\core\src\miscellaneous\CoreFunctions;
use \app\models\CronModel;

class Cron {

    protected array $cronjobs;

    protected function getCronjobs(): array {
        return $this->cronjobs;
    }

    protected function setCronjobs(array $cronjobs): void {
        $this->cronjobs = $cronjobs;
    }

    public function run() {
        foreach ((new CronModel())->all() as $cronJob) {
            $cCronjob = (new CronjobFactory(['handler' => $cronJob->get('CronjobEntity')]))->create();
            foreach ($cCronjob->getCronjobs() as $cronjob) {
                try {
                    CoreFunctions::app()->addSystemEvent(['Starting new cronjob iteration ' . $cronjob]);
                    $cCronjob->{$cronjob}();
                    CoreFunctions::app()->addSystemEvent(['Cronlog ran without errors: ' . $cronjob]);
                } catch (\Exception $e) {
                    CoreFunctions::app()->addSystemEvent(['Cronlog ran with errors: ' . json_encode($e)]);
                }
            }
        }
    }

}