<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 12:18
 */

namespace App\Console\Commands\Client;


use Illuminate\Console\Command;


class RunQuery extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'client:run {query}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run CQuery';


    /**
     * Command Runner
     */
    public function handle()
    {
        $inputData = $this->processArguments();

    }

    /**
     * Function used to process arguments and return a formatted array based on received information
     * @return array
     */
    protected function processArguments()
    {
        $inputData = [
            'query' => $this->argument('query'),

        ];


        return $inputData;
    }
}