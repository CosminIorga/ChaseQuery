<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:05
 */

namespace App\Jobs\Server\Worker;


use App\Definitions\Worker;
use App\Exceptions\WorkerException;
use App\Jobs\Job;
use App\Traits\CustomConsoleOutput;
use Illuminate\Support\Collection;

class StartWorkersJob extends Job
{
    use CustomConsoleOutput;

    /**
     * Variable used to hold the number of gearman workers
     * @var null
     */
    protected $workerCount = null;

    /**
     * Array used to store worker instances
     * @var Collection
     */
    protected $workers = null;

    /**
     * StartWorkersJob constructor.
     * @param array $inputData
     */
    public function __construct($inputData = [])
    {
        /* Initialize variables */
        $this->init();

        /* Process input data */
        $this->processInputData($inputData);

    }

    /**
     * Function called by constructor to initialize variables where default values are not supported
     */
    protected function init()
    {
        $this->workers = new Collection();
    }

    /**
     * Function used to process received job data
     * @param $inputData
     * @throws WorkerException
     */
    protected function processInputData($inputData)
    {
        if (!array_key_exists(Worker::WORKER_COUNT, $inputData)) {
            throw new WorkerException(Worker::NO_WORKER_KEY);
        }

        if (!is_numeric($inputData[Worker::WORKER_COUNT]) || $inputData[Worker::WORKER_COUNT] < 1) {
            throw new WorkerException(Worker::INVALID_WORKER_VALUE);
        }

        $this->workerCount = $inputData[Worker::WORKER_COUNT];
    }

    /**
     * Method called when job is executed
     */
    public function handle()
    {
        /* Initialize workers */
        $this->initializeWorkers();

        /* Run workers */
        $this->runWorkers();
    }

    /**
     * Function used to initialize workers and submit available tasks to them
     */
    protected function initializeWorkers()
    {
        for ($workerId = 0; $workerId < $this->workerCount; $workerId++) {
            $worker = new \GearmanWorker();

            $worker->addServer();

            /* All workers can do any task */
            foreach (Worker::WORKER_FUNCTIONS as $workerFunctionName => $workerClass) {
                $worker->addFunction($workerFunctionName, [
                    new $workerClass(),
                    $workerFunctionName
                ]);
            }

            $this->workers->put($workerId, $worker);
        }
    }

    /**
     * Function used to start workers and wait for worker response
     */
    protected function runWorkers()
    {
        $this->workers->each(function (\GearmanWorker $worker, int $workerId) {
            $pid = pcntl_fork();

            if (!$pid) {
                $this->info("[Worker $workerId] Started");
                while ($worker->work()) {
                }
                exit($workerId);
            }
        });

        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
            $this->warn("[Worker $status] Completed");
        }
    }
}