<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/03/17
 * Time: 12:41
 */

namespace Jobs\Server\Master;


use App\Jobs\Job;
use Models\TablesInformationModel;

class GetTablesInformation extends Job
{
    /**
     * Job attributes
     */
    const QUEUE_NAME = "getTablesInformation";
    const CONNECTION = "sync";

    /**
     * Variable used to specify tables information file
     * @var string
     */
    protected $tablesInformationFileRelativeToStoragePath = "volatile/Tables.vlt.ini";

    /**
     * Variable used to specify tables information full file path
     * @var string
     */
    protected $tablesInformationFileFullPath;

    /**
     * Array used to store given table names
     * @var array
     */
    protected $tableNames = [];

    /**
     * Array used to store tables information
     * @var array
     */
    protected $tablesInformation = [];

    /**
     * GetTablesInformation constructor.
     * @param array $tableNames
     */
    public function __construct(array $tableNames)
    {
        $this->init($tableNames);

        $this->computeTablesInformationFilePath();
    }

    /**
     * Function used to initialize fields
     * @param array $tableNames
     */
    protected function init(array $tableNames)
    {
        $this->tableNames = $tableNames;
    }

    /**
     * Function used to retrieve "Tables.vlt.ini" full file path
     */
    protected function computeTablesInformationFilePath()
    {
        $this->tablesInformationFileFullPath = storage_path($this->tablesInformationFileRelativeToStoragePath);
    }

    /**
     * Job runner
     * @return array
     */
    public function handle()
    {
        $allTablesInformation = $this->getTablesInformationFromFile();

        return $this->parseTablesInformation($allTablesInformation);
    }

    /**
     * Function used to retrieve tables information from file
     */
    protected function getTablesInformationFromFile(): array
    {
        $connections = parse_ini_file($this->tablesInformationFileFullPath, true);

        return $connections;
    }

    /**
     * Function used to extract necessary tables from all tables information data
     * @param array $allTablesInformation
     * @return array
     */
    protected function parseTablesInformation(array $allTablesInformation): array
    {
        $tablesInformation = array_intersect_key($allTablesInformation, array_flip($this->tableNames));

        array_walk($tablesInformation, function ($tableInformation) use (&$tablesInformationModels) {
            $tablesInformationModels[] = new TablesInformationModel($tableInformation);
        });

        return $tablesInformationModels;
    }
}