<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 16:45
 */

namespace App\Console\Commands\Server;


use App\Jobs\Server\StartWorkersJob;
use Illuminate\Console\Command;

class StartWorkers extends Command
{


    /**
     * Queue name
     * @var string
     */
    const QUEUE_NAME = 'StartWorkers';

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'worker:start {workerCount=16}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Start Gearman workers with registered functions';


    /**
     * Command Runner
     */
    public function handle()
    {
        $inputData = $this->processArguments();

        $job = (new StartWorkersJob($inputData))->onQueue(self::QUEUE_NAME);

        $job->onConnection('sync');

        dispatch($job);
    }

    /**
     * Function used to process arguments and return a formatted array based on received information
     * @return array
     */
    protected function processArguments(): array
    {
        $inputData = [
            'workerCount' => $this->argument('workerCount')
        ];

        return $inputData;
    }
}