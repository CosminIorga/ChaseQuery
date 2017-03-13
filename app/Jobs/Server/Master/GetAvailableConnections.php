<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 16:29
 */

namespace Jobs\Server\Master;


use App\Jobs\Job;
use Models\DatabaseConnectionModel;

class GetAvailableConnections extends Job
{
    /**
     * Job attributes
     */
    const QUEUE_NAME = "getConnections";
    const CONNECTION = "sync";

    /**
     * Variable used to specify connections file
     * @var string
     */
    protected $connectionsFileRelativeToStoragePath = "volatile/Heartbeat.vlt.ini";

    /**
     * Variable used to specify connections full file path
     * @var string
     */
    protected $connectionsFileFullPath;

    /**
     * Array of DatabaseConnectionsModel used to store all available database connections
     * @var array
     */
    protected $connections = [];

    /**
     * GetAvailableConnections constructor.
     */
    public function __construct()
    {
        $this->computeConnectionsFilePath();
    }

    /**
     * Function used to retrieve "Heartbeat.vlt.ini" full file path
     */
    protected function computeConnectionsFilePath()
    {
        $this->connectionsFileFullPath = storage_path($this->connectionsFileRelativeToStoragePath);
    }


    /**
     * Job runner
     * @return array
     */
    public function handle()
    {
        $connectionsData = $this->getAvailableConnectionsFromFile();

        return $this->parseConnectionsData($connectionsData);
    }

    /**
     * Function used to retrieve connections information from file
     * @return array
     */
    protected function getAvailableConnectionsFromFile(): array
    {
        $connections = parse_ini_file($this->connectionsFileFullPath, true);

        return $connections;
    }

    /**
     * Function used to encapsulate connections into models
     * @param array $connectionsData
     * @return array
     */
    protected function parseConnectionsData(array $connectionsData): array
    {
        $connections = [];

        array_walk($connectionsData, function ($connectionData) use (&$connections) {
            $connections = new DatabaseConnectionModel($connectionData);
        });

        return $connections;
    }
}