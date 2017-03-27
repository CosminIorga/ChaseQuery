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
    protected function announce(int $id)
    {
        $message = "[Worker $id] Received task: " . class_basename($this);
        $this->comment($message);
    }
}