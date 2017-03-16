<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 16:29
 */

namespace App\Jobs\Server\Master;


use App\Jobs\Job;
use App\Models\DatabaseModel;

class GetAvailableDatabases extends Job
{
    /**
     * Job attributes
     */
    const QUEUE_NAME = "getDatabases";
    const CONNECTION = "sync";

    /**
     * Variable used to specify databases file
     * @var string
     */
    protected $databasesFileRelativeToStoragePath = "volatile/Heartbeat.vlt.ini";

    /**
     * Variable used to specify databases full file path
     * @var string
     */
    protected $databasesFileFullPath;

    /**
     * Array of DatabaseModel used to store all available databases
     * @var array
     */
    protected $databases = [];

    /**
     * GetAvailableDatabases constructor.
     */
    public function __construct()
    {
        $this->computeDatabasesFilePath();
    }

    /**
     * Function used to retrieve "Heartbeat.vlt.ini" full file path
     */
    protected function computeDatabasesFilePath()
    {
        $this->databasesFileFullPath = storage_path($this->databasesFileRelativeToStoragePath);
    }


    /**
     * Job runner
     * @return array
     */
    public function handle()
    {
        $databasesData = $this->getAvailableDatabasesFromFile();

        return $this->parseDatabasesData($databasesData);
    }

    /**
     * Function used to retrieve databases information from file
     * @return array
     */
    protected function getAvailableDatabasesFromFile(): array
    {
        $databases = parse_ini_file($this->databasesFileFullPath, true);

        return $databases;
    }

    /**
     * Function used to encapsulate databases into models
     * @param array $databasesData
     * @return array
     */
    protected function parseDatabasesData(array $databasesData): array
    {
        $databases = [];

        array_walk($databasesData, function ($databaseData) use (&$databases) {
            $databases[] = new DatabaseModel($databaseData);
        });

        return $databases;
    }
}