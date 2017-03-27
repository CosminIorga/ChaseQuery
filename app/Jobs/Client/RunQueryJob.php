<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 12:41
 */

namespace App\Jobs\Client;


use App\Definitions\Worker;
use App\Interfaces\Payload;
use App\Jobs\Job;
use App\Jobs\Server\Master\ComputeReducerPayloads;
use App\Models\ResponseModel as Response;
use App\Models\ReturnDataModel;
use GearmanClient;
use GearmanTask;
use App\Jobs\Server\Master\ComputeMapperPayloads;
use App\Models\CQueryModel;

class RunQueryJob extends Job
{
    const CONNECTION = "sync";
    const QUEUE_NAME = "runQuery";

    /**
     * Variable used store the CQuery model
     * @var CQueryModel
     */
    protected $cQueryModel;

    /**
     * Variable used to store the Gearman Client
     * @var GearmanClient
     */
    protected $gearmanClient;

    /**
     * Variable used to store mapper data
     * @var array
     */
    protected $mapperData = [];

    /**
     * Variable used to store reducer data
     * @var array
     */
    protected $reducerData = [];

    /**
     * RunQueryJob constructor.
     * @param array $inputData
     */
    public function __construct(array $inputData)
    {
        /* Initialize fields */
        $this->init($inputData);

        /* Initialize Gearman Client */
        $this->initGearman();
    }

    /**
     * Function called by constructor to initialize fields where default values are not supported
     * @param array $inputData
     */
    protected function init(array $inputData)
    {
        $this->cQueryModel = new CQueryModel($inputData);
    }

    /**
     * Initialize various Gearman related elements
     */
    protected function initGearman()
    {
        $this->gearmanClient = new GearmanClient();

        $gearmanHost = config('gearman.host');
        $gearmanPort = config('gearman.port');

        $this->gearmanClient->addServer($gearmanHost, $gearmanPort);

        $this->gearmanClient->setCompleteCallback(function (GearmanTask $task, $context) {
            switch ($context) {
                case Worker::WORKER_MAP:
                    $data = json_decode($task->data(), true);

                    $this->mapperData = array_merge($this->mapperData, $data);
                    break;

                case Worker::WORKER_REDUCE:
                    $data = json_decode($task->data(), true);

                    $this->reducerData = array_merge($this->reducerData, $data);
                    break;
                default:
                    /* It won't reach this branch */
                    break;
            }
        });
    }

    /**
     * Job runner
     */
    public function handle()
    {
        try {
            /* Start timer for performance benchmarks */
            $startTime = microtime(true);

            /* Create "mapper" payloads based on CQuery Model */
            $mapperPayloads = $this->createMapperPayloads();

            /* Dispatch "mapper" payloads to Gearman workers */
            $mapperTime = $this->dispatchPayloads($mapperPayloads);

            /* Create "reducer" payloads based on CQuery Model and computed data */
            $reducerPayloads = $this->createReducerPayloads();

            /* Dispatch "reducer" payloads to Gearman workers */
            $reducerTime = $this->dispatchPayloads($reducerPayloads);

            /* Compute total operations time */
            $endTime = microtime(true);
            $totalTime = $endTime - $startTime;

            /* Return reduced data */
            $response = (new Response())
                ->success()
                ->content(new ReturnDataModel(
                    $mapperTime,
                    $reducerTime,
                    $totalTime,
                    $this->mapperData,
                    $this->reducerData
                ));

        } catch (\Exception $exception) {
            $response = (new Response())
                ->failure()
                ->message($exception->getMessage())
                ->content(null);
        }

        return $response;
    }

    /**
     * Small function used to create mapper payloads
     * @return array
     */
    protected function createMapperPayloads(): array
    {
        $job = (new ComputeMapperPayloads($this->cQueryModel))
            ->onQueue(ComputeMapperPayloads::QUEUE_NAME)
            ->onConnection(ComputeMapperPayloads::CONNECTION);

        return dispatch($job);
    }

    /**
     * Small function used to create reducer payloads
     * @return array
     */
    protected function createReducerPayloads(): array
    {
        $job = (new ComputeReducerPayloads($this->mapperData, $this->cQueryModel))
            ->onQueue(ComputeReducerPayloads::QUEUE_NAME)
            ->onConnection(ComputeReducerPayloads::CONNECTION);

        return dispatch($job);
    }


    /**
     * Function used to
     * @param array $payloads
     * @return float
     */
    protected function dispatchPayloads(array $payloads): float
    {
        array_walk($payloads, function (
            /** @var Payload $payload */
            $payload,
            /** @var integer $index */
            $index
        ) {
            $this->gearmanClient->addTask(
                $payload->getFunction(),
                $payload,
                $payload->getFunction(),
                $index
            );
        });

        $start = microtime(true);
        $this->gearmanClient->runTasks();
        $end = microtime(true);

        return $end - $start;
    }
}