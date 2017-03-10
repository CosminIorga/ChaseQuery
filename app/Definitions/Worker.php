<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:13
 */

namespace App\Definitions;

use App\Jobs\Server\Tasks\Mapper;
use App\Jobs\Server\Tasks\Reducer;

/**
 * Class Worker
 * @package App\Definitions\Worker
 */
final class Worker
{


    # Number of gearman workers
    const WORKER_COUNT = "workerCount";

    # Mapping function
    const WORKER_MAP = "map";
    const WORKER_MAP_CLASS = Mapper::class;
    # Reduce function
    const WORKER_REDUCE = "reduce";
    const WORKER_REDUCE_CLASS = Reducer::class;

    # All available worker functions
    const WORKER_FUNCTIONS = [
        self::WORKER_MAP => self::WORKER_MAP_CLASS,
        self::WORKER_REDUCE => self::WORKER_REDUCE_CLASS
    ];

    # Worker exception messages
    const NO_WORKER_KEY = "Worker count key not found in input data";
    const INVALID_WORKER_VALUE = "Worker count variable is of invalid type or value";
}