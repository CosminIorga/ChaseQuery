<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/03/17
 * Time: 12:41
 */

namespace App\Jobs\Server\Master;


use App\Jobs\Job;
use App\Models\CQueryModel;
use App\Models\TableInformationModel;
use App\Repositories\CommonRepository;

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
     * Variable used to store the CQueryModel
     * @var CQueryModel
     */
    protected $CQueryModel = [];

    /**
     * Array used to store tables information
     * @var array
     */
    protected $tablesInformation = [];

    /**
     * Repository containing useful queries
     * @var CommonRepository
     */
    protected $commonRepository;
    /**
     * GetTablesInformation constructor.
     * @param CQueryModel $CQueryModel
     */
    public function __construct(CQueryModel $CQueryModel)
    {
        $this->init($CQueryModel);

        $this->computeTablesInformationFilePath();
    }

    /**
     * Function used to initialize fields
     * @param CQueryModel $CQueryModel
     */
    protected function init(CQueryModel $CQueryModel)
    {
        $this->CQueryModel = $CQueryModel;
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
     * @param CommonRepository $commonRepository
     * @return array
     */
    public function handle(
        CommonRepository $commonRepository
    ) {
        $this->commonRepository = $commonRepository;

        $allTablesInformation = $this->getTablesInformationFromFile();

        $tablesInformation = $this->parseTablesInformation($allTablesInformation);

        return $this->refineTablesInformation($tablesInformation);
    }

    /**
     * Function used to retrieve tables information from file
     */
    protected function getTablesInformationFromFile(): array
    {
        $tables = parse_ini_file($this->tablesInformationFileFullPath, true);

        return $tables;
    }

    /**
     * Function used to extract necessary tables from all tables information data
     * @param array $allTablesInformation
     * @return array
     */
    protected function parseTablesInformation(array $allTablesInformation): array
    {
        $tablesInformation = array_intersect_key($allTablesInformation, array_flip($this->CQueryModel->getTables()));

        $tablesInformationModels = [];
        array_walk($tablesInformation, function ($tableInformation) use (&$tablesInformationModels) {
            $tablesInformationModels[] = new TableInformationModel($tableInformation);
        });

        return $tablesInformationModels;
    }

    /**
     * Function used to check which partitions are necessary for current query
     * @param $tablesInformation
     * @return array
     */
    protected function refineTablesInformation($tablesInformation): array
    {
        array_walk($tablesInformation, function (
            /** @var TableInformationModel $tableInformation */
            &$tableInformation
        ) {
            /* Skip checking for partitions if table is not partitioned */
            if (!$tableInformation->isPartitioned()) {
                return;
            }

            /* Compute complete query */
            $query = $this->CQueryModel->injectTableIntoQuery(
                $tableInformation->getAttribute(TableInformationModel::TABLE_NAME)
            );

            /* Get used partitions for each table */
            $partitions = $this->commonRepository->getUsedPartitionsForQuery($query);

            $partitions = explode(',', $partitions);

            $tableInformation->setUsedTablePartitions($partitions);
        });

        return $tablesInformation;
    }



}