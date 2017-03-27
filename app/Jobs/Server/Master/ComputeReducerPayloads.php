<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/03/17
 * Time: 13:18
 */

namespace App\Jobs\Server\Master;


use App\Jobs\Job;
use App\Models\CQueryModel;
use App\Models\ReducerPayloadModel;

class ComputeReducerPayloads extends Job
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
     * Variable used to hold the computed mapped data
     * @var array
     */
    protected $mappedData;

    /**
     * Single-value array of ReducerPayloadModel
     * @var array
     */
    protected $payload;


    /**
     * ComputeMapperPayloads constructor.
     * @param array $mappedData
     * @param CQueryModel $CQueryModel
     */
    public function __construct(array $mappedData, CQueryModel $CQueryModel)
    {
        $this->init($mappedData, $CQueryModel);
    }

    /**
     * Function called by constructor to initialize fields where default values are not supported
     * @param array $mappedData
     * @param CQueryModel $CQueryModel
     */
    protected function init(array $mappedData, CQueryModel $CQueryModel)
    {
        $this->mappedData = $mappedData;
        $this->CQueryModel = $CQueryModel;
    }

    /**
     * Job runner
     */
    public function handle()
    {
        /* Compute payload model */
        $this->computePayloadModel();

        return $this->payload;
    }

    /**
     * Function used to compute the reducer payload
     */
    protected function computePayloadModel()
    {
        $this->payload[] = new ReducerPayloadModel($this->mappedData, $this->CQueryModel->getGroupColumns());
    }
}