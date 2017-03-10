<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:41
 */

namespace App\Jobs\Server\Tasks;


use App\Jobs\Job;

class Reducer extends Job
{

    public function reduce()
    {
        echo "In reducer " . PHP_EOL;

    }
}