<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:40
 */

namespace App\Jobs\Server\Worker\Tasks;


use App\Models\MapperPayloadModel;
use DB;
use GearmanJob;
use Illuminate\Database\Connection;

class Mapper extends DefaultTask
{
    /**
     * Payload
     * @var MapperPayloadModel
     */
    protected $payload;

    /**
     * Database Connection
     * @var Connection
     */
    protected $databaseConnector;

    /**
     * Function called by gearman when "Map" function is requested
     * @param GearmanJob $job
     * @return array
     */
    public function map(GearmanJob $job)
    {
        $this->announce((int) $job->unique());

        $this->init($job);

        $data = $this->fetchData();

        return $this->encodeReturnData($data);
    }

    /**
     * Function used to init fields
     * @param GearmanJob $job
     */
    public function init(GearmanJob $job)
    {
        $workload = $job->workload();

        /* Init payload */
        $this->payload = unserialize($workload);

        /* Init a new DB connection */
        $this->initDatabaseConnection();
    }

    /**
     * Function used to create a database connection
     */
    protected function initDatabaseConnection()
    {
        $databaseName = $this->payload->getDatabaseConfig()->getDatabaseName();

        $this->databaseConnector = DB::connection($databaseName);
    }

    /**
     * Function used to query the database and fetch data
     * @return array
     */
    public function fetchData(): array
    {
        $query = $this->payload->getPayloadQuery();

        $data = $this->databaseConnector->select($query);

        return $data;
    }
}