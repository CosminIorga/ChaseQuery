<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 17:35
 */

namespace Jobs\Server\Master;


use App\Jobs\Job;
use Models\CQueryModel;

class ComputeMapperPayloads extends Job
{


    /**
     * Job attributes
     */
    const QUEUE_NAME = "computeMapperPayloads";
    const CONNECTION = "sync";

    /**
     * Variable used to hold the CQueryModel
     * @var CQueryModel
     */
    protected $CQueryModel;

    /**
     * Array of DatabaseConnectionsModel
     * @var array
     */
    protected $databaseConnections = [];

    /**
     * Array of TablesInformationModel
     * @var array
     */
    protected $tablesInformation = [];

    /**
     * ComputeMapperPayloads constructor.
     * @param CQueryModel $CQueryModel
     */
    public function __construct(CQueryModel $CQueryModel)
    {
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
        /* Get available database connections */
        $this->getAvailableDatabaseConnections();

        /* Get tables information */
        $this->getTablesInformation();

        /* Compute payloads */
        $this->computePayloads();
    }

    /**
     * Function used to get available database connections
     */
    protected function getAvailableDatabaseConnections()
    {
        $job = (new GetAvailableConnections())
            ->onQueue(GetAvailableConnections::QUEUE_NAME)
            ->onConnection(GetAvailableConnections::CONNECTION);

        $this->databaseConnections = dispatch($job);
    }

    /**
     * Function used to get tables information
     */
    protected function getTablesInformation()
    {
        $job = (new GetTablesInformation($this->CQueryModel->getTables()))
            ->onQueue(GetTablesInformation::QUEUE_NAME)
            ->onConnection(GetTablesInformation::CONNECTION);

        $this->tablesInformation = dispatch($job);
    }


    protected function computePayloads()
    {

    }
}