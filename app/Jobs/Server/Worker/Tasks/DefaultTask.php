<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/03/17
 * Time: 12:50
 */

namespace App\Jobs\Server\Worker\Tasks;


use App\Traits\CustomConsoleOutput;
use GearmanJob;

abstract class DefaultTask
{
    use CustomConsoleOutput;

    private $startTime = null;
    private $endTime = null;

    /**
     * Function used to init fields
     * @param GearmanJob $job
     */
    abstract protected function init(GearmanJob $job);

    /**
     * Function used to query the database and fetch data
     * @return array
     */
    abstract protected function fetchData(): array;

    /**
     * Function used to encode return data
     * @param array $returnData
     * @return string
     */
    protected function encodeReturnData(array $returnData): string
    {
        return json_encode($returnData);
    }

    /**
     * Short function used to announce each task
     * @param int $id
     */
    protected function announceStart(int $id)
    {
        /* Start timer for performance benchmarks */
        $this->startTime = microtime(true);

        $id = str_pad($id, 2, 0, STR_PAD_LEFT);

        $message = "[Worker $id] Started task: " . class_basename($this);
        $this->comment($message);
    }

    protected function announceEnd(int $id)
    {
        $elapsed = '';
        $this->endTime = microtime(true);
        $id = str_pad($id, 2, 0, STR_PAD_LEFT);

        if (!is_null($this->startTime)) {
            $elapsed = " (Elapsed: " . ($this->endTime - $this->startTime) . ")";
        }

        $message = "[Worker $id] Finished task: " . class_basename($this) . $elapsed;
        $this->comment($message);
    }
}