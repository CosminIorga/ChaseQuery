<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:41
 */

namespace App\Jobs\Server\Worker\Tasks;


use App\Models\ReducerPayloadModel;
use GearmanJob;

class Reducer extends DefaultTask
{

    /**
     * Payload
     * @var ReducerPayloadModel
     */
    protected $payload;

    /**
     * Function called by gearman when "Reduce" function is requested
     * @param GearmanJob $job
     * @return string
     */
    public function reduce(GearmanJob $job)
    {
        $this->announce($job->unique());

        $this->init($job);

        $data = $this->fetchData();

        return $this->encodeReturnData($data);
    }

    /**
     * Function used to init fields
     * @param GearmanJob $job
     */
    protected function init(GearmanJob $job)
    {
        $workload = $job->workload();

        /* Init payload */
        $this->payload = unserialize($workload);
    }

    /**
     * Function used to query the database and fetch data
     * @return array
     */
    protected function fetchData(): array
    {
        $groupByColumns = $this->payload->getGroupColumns();
        $mappedData = $this->payload->getMappedData();

        $reducedData = array_reduce($mappedData, function ($carry, $record) use ($groupByColumns) {
            /* Compute unique key based on group by column values */
            $uniqueColumns = array_map(function ($groupColumn) use ($record) {
                return array_key_exists($groupColumn, $record) ? $record[$groupColumn] : 0;
            }, $groupByColumns);

            $uniqueKey = base64_encode(json_encode($uniqueColumns));

            /* Create carry as array on first iteration */
            if (!is_array($carry)) {
                $carry = [];
            }

            /* If uniqueKey doesn't exist, create it */
            if (!array_key_exists($uniqueKey, $carry)) {
                $carry[$uniqueKey] = $record;
                return $carry;
            }

            /* If uniqueKey exists, aggregate data */
            foreach (array_keys($carry[$uniqueKey] + $record) as $key) {
                /* Don't overwrite groupBy column as it's identical */
                if (in_array($key, $groupByColumns)) {
                    continue;
                }
                /* Sum data otherwise */
                $carry[$uniqueKey][$key] = (isset($carry[$uniqueKey][$key]) ? $carry[$uniqueKey][$key] : 0) +
                    (isset($record[$key]) ? $record[$key] : 0);
            }

            return $carry;
        });

        return $reducedData;
    }
}