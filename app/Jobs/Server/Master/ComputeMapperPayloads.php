<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 17:35
 */

namespace App\Jobs\Server\Master;


use App\Jobs\Job;
use App\Models\CQueryModel;
use App\Models\DatabaseModel;
use App\Models\MapperPayloadModel;
use App\Models\TableInformationModel;

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
     * Array of MapperPayloadModel
     * @var array
     */
    protected $payloads = [];

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

        /* Compute payload models */
        $this->computePayloadModels();

        return $this->payloads;
    }

    /**
     * Function used to get available database connections
     */
    protected function getAvailableDatabaseConnections()
    {
        $job = (new GetAvailableDatabases())
            ->onQueue(GetAvailableDatabases::QUEUE_NAME)
            ->onConnection(GetAvailableDatabases::CONNECTION);

        $this->databaseConnections = dispatch($job);
    }

    /**
     * Function used to get tables information
     */
    protected function getTablesInformation()
    {
        $job = (new GetTablesInformation($this->CQueryModel))
            ->onQueue(GetTablesInformation::QUEUE_NAME)
            ->onConnection(GetTablesInformation::CONNECTION);

        $this->tablesInformation = dispatch($job);
    }

    /**
     * Function used to compute payloads depending on number of possible DB connections and table partitions
     */
    protected function computePayloadModels()
    {
        $payloads = [];

        /* Sort tablesInformation by non-partitioned tables first */
        usort($this->tablesInformation, function (
            /** @var TableInformationModel $tableInformation1 */
            /** @var TableInformationModel $tableInformation2 */
            $tableInformation1,
            $tableInformation2
        ) {
            if ($tableInformation1->isPartitioned()) {
                return 0;
            }

            if ($tableInformation2->isPartitioned()) {
                return 1;
            }

            return 0;
        });

        /** @var TableInformationModel $tableInformation */
        foreach ($this->tablesInformation as $tableInformation) {
            if ($tableInformation->isPartitioned()) {
                /* Iterate through table partitions if table is partitioned */
                foreach ($tableInformation->getUsedTablePartitions() as $partition) {
                    /* Get database with lowest load */
                    $databaseId = $this->getDatabaseByLowestLoad();

                    /* Save association (database, table, partition) */
                    $payloads[$databaseId][$tableInformation->getTableName()][] = $partition;

                    /* Update database load */
                    $this->databaseConnections[$databaseId]->addLoad(config('metrics.loads.partition'));
                }
            } else {
                /* Get database with lowest load */
                $databaseId = $this->getDatabaseByLowestLoad();

                /* Save association (database, table) */
                $payloads[$databaseId][$tableInformation->getTableName()] = [];

                /* Update database load */
                $this->databaseConnections[$databaseId]->addLoad(config('metrics.loads.table'));
            }
        }

        $payloadModels = [];

        /* Iterate through payloads data and compute PayloadModels */
        foreach ($payloads as $databaseId => $tableInformation) {
            foreach ($tableInformation as $tableName => $partitions) {
                if (config('common.force_splitting_by_partitions')) {
                    $maxPartitions = config('common.partition_granularity');

                    $offset = 0;
                    while ($currentPartitions = array_slice($partitions, $offset, $maxPartitions)) {
                        /* Compute payload query */
                        $payloadQuery = $this->computePayloadQuery($tableName, $currentPartitions);

                        $payloadModels[] = new MapperPayloadModel(
                            $this->databaseConnections[$databaseId],
                            $payloadQuery
                        );

                        $offset += $maxPartitions;
                    }

                } else {
                    /* Compute payload query */
                    $payloadQuery = $this->computePayloadQuery($tableName, $partitions);

                    $payloadModels[] = new MapperPayloadModel(
                        $this->databaseConnections[$databaseId],
                        $payloadQuery
                    );
                }
            }
        }

        $this->payloads = $payloadModels;
    }

    /**
     * Function used to compute payload query
     * @param string $tableName
     * @param array $partitions
     * @return string
     */
    protected function computePayloadQuery(string $tableName, array $partitions): string
    {
        if (!empty($partitions)) {
            $tableName .= " PARTITION (" . implode(", ", $partitions) . ") ";
        }

        $payloadQuery = $this->CQueryModel->injectTableIntoQuery($tableName);

        return $payloadQuery;
    }


    /**
     * Function used to return database index with lowest load
     * @return int
     */
    protected function getDatabaseByLowestLoad(): int
    {
        $databaseIndexWithLowestLoad = null;
        $databaseLoad = null;

        /** @var DatabaseModel $databaseConnection */
        foreach ($this->databaseConnections as $databaseIndex => $databaseConnection) {
            if (is_null($databaseLoad) || $databaseLoad > $databaseConnection->getLoad()) {
                $databaseLoad = $databaseConnection->getLoad();
                $databaseIndexWithLowestLoad = $databaseIndex;
            }
        }

        return $databaseIndexWithLowestLoad;
    }
}