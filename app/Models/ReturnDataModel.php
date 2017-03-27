<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 21/03/17
 * Time: 15:22
 */

namespace App\Models;


class ReturnDataModel extends NonPersistentModel
{
    /**
     * Data times
     */
    const DATA_TIMES = "data_times";
    const MAPPING_TIME = "mapping_time";
    const REDUCING_TIME = "reducing_time";
    const TOTAL_TIME = "total_time";

    /**
     * Data
     */
    const DATA = "data";
    const INTERMEDIATE_DATA = "intermediate_data";
    const FINAL_DATA = "final_data";

    /**
     * ReturnDataModel constructor.
     * @param float $mapTime
     * @param float $reduceTime
     * @param float $totalTime
     * @param array $intermediateData
     * @param array $finalData
     */
    public function __construct(
        float $mapTime,
        float $reduceTime,
        float $totalTime,
        array $intermediateData,
        array $finalData
    ) {
        $attributes = [
            self::DATA_TIMES => [
                self::MAPPING_TIME => $mapTime,
                self::REDUCING_TIME => $reduceTime,
                self::TOTAL_TIME => $totalTime
            ],
            self::DATA => [
                self::INTERMEDIATE_DATA => $intermediateData,
                self::FINAL_DATA => $finalData
            ]
        ];

        parent::__construct($attributes);
    }

    /**
     * Function used to return times of query
     * @return \stdClass
     */
    public function getTimes()
    {
        return new class($this)
        {
            public $mappingTime;
            public $reducingTime;
            public $totalTime;

            function __construct(
                ReturnDataModel $returnDataModel
            ) {
                $times = $returnDataModel->getAttribute($returnDataModel::DATA_TIMES);
                $this->mappingTime = $times[$returnDataModel::MAPPING_TIME];
                $this->reducingTime = $times[$returnDataModel::REDUCING_TIME];
                $this->totalTime = $times[$returnDataModel::TOTAL_TIME];
            }

            /**
             * Get mapping time
             * @return float
             */
            function getMappingTime(): float
            {
                return $this->mappingTime;
            }

            /**
             * Get reducing time
             * @return float
             */
            public function getReducingTime(): float
            {
                return $this->reducingTime;
            }

            /**
             * Get total time
             * @return float
             */
            public function getTotalTime(): float
            {
                return $this->totalTime;
            }
        };
    }

    /**
     * Function used to retrieve query data
     * @return \stdClass
     */
    public function getData()
    {
        return new class($this)
        {
            public $intermediateData;
            public $finalData;

            function __construct(
                ReturnDataModel $returnDataModel
            ) {
                $data = $returnDataModel->getAttribute($returnDataModel::DATA);

                $this->intermediateData = $data[$returnDataModel::INTERMEDIATE_DATA];
                $this->finalData = $data[$returnDataModel::FINAL_DATA];
            }

            /**
             * Getter for intermediate data
             * @return array
             */
            public function getIntermediateData(): array
            {
                return $this->intermediateData;
            }

            /**
             * Getter for final data
             * @return array
             */
            public function getFinalData(): array
            {
                return $this->finalData;
            }
        };
    }

}