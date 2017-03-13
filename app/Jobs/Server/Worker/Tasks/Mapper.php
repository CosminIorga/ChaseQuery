<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:40
 */

namespace App\Jobs\Server\Worker\Tasks;


use App\Jobs\Job;
use GearmanJob;

class Mapper extends Job
{


    public function map(GearmanJob $job)
    {
        echo "In mapper " . PHP_EOL;
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        /** @var  $workload */
        $workload = $job->workload();

        var_dump($workload);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

    }
}