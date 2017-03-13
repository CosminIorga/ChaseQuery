<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 12:31
 */

namespace Jobs\Server\Master;


use App\Jobs\Job;
use Models\CQueryModel;
use Traits\HeartBeat;
use Traits\TablesInformation;

class ComputeQueries extends Job
{
    use HeartBeat;
    use TablesInformation;

    const QUEUE_NAME = "computeQueries";
    const CONNECTION = "sync";

    /**
     * Variable used to hold the CQueryModel
     * @var CQueryModel
     */
    protected $CQueryModel;

    /**
     * RunQueryJob constructor.
     * @param CQueryModel $CQueryModel
     */
    public function __construct(CQueryModel $CQueryModel)
    {
        /* Initialize fields */
        $this->init($CQueryModel);
    }

    /**
     * Function called by constructor to initialize fields where default values are not supported
     * @param CQueryModel $CQueryModel
     */
    protected function init(CQueryModel $CQueryModel)
    {
        $this->CQueryModel = $CQueryModel;
    }

    /**
     * Job runner
     */
    public function handle()
    {
        /* Get current available connections */
        $connections = $this->getAvailableConnections();

        /* Get information regarding given tables */
        $tablesInformation = $this->getTablesInformation($this->CQueryModel->getTables());

        /* Decide partitions for each table per connection */
        $this->decidePartitions($connections, $tablesInformation);
    }

    /**
     * Function used to decide
     * @param array $connections
     * @param array $tablesInformation
     */
    protected function decidePartitions(array $connections, array $tablesInformation)
    {



    }

}