<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 09/03/17
 * Time: 15:52
 */

namespace Models;


use App\Models\NonPersistentModel;

class CQueryModel extends NonPersistentModel
{

    const QUERY = "query";
    const TABLES = "tables";
    const GROUP_COLUMNS = "group_columns";

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::QUERY => 'required|string',
        self::TABLES => 'required|array',
        self::GROUP_COLUMNS => 'required|array'
    ];

    /**
     * Array used to store computed queries based on received data
     * @var array
     */
    protected $computedQueries = [];

    /**
     * Variable used to trigger an exception if data validation fails
     * @var bool
     */
    protected $throwExceptionOnValidationFail = true;


    /**
     * Function used to retrieve queries that result from given data
     * @return array
     */
    public function getQueries(): array
    {
        if (empty($this->computedQueries)) {
            $this->computeQueries();
        }

        return $this->computedQueries;
    }

    protected function computeQueries()
    {



    }
}