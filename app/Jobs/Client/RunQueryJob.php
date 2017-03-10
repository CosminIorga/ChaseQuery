<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 12:41
 */

namespace Jobs\Client;


use App\Jobs\Job;
use App\Models\ResponseModel;
use App\Validators\ArrayStructureValidator;
use App\Validators\Validator;
use Models\CQueryModel;

class RunQueryJob extends Job
{

    /**
     * Variable used store the CQuery model
     * @var CQueryModel
     */
    protected $payload;


    /**
     * RunQueryJob constructor.
     * @param array $inputData
     */
    public function __construct(array $inputData)
    {
        /* Initialize fields */
        $this->init($inputData);
    }

    /**
     * Function called by constructor to initialize fields where default values are not supported
     * @param array $inputData
     */
    protected function init(array $inputData)
    {
        /* Create payload model */
        $this->payload = new CQueryModel($inputData);

    }

    public function handle()
    {
    }
}