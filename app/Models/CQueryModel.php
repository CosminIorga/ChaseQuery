<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 09/03/17
 * Time: 15:52
 */

namespace App\Models;



class CQueryModel extends NonPersistentModel
{
    const QUERY = "query";
    const TABLES = "tables";
    const GROUP_COLUMNS = "groupColumns";

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
     * Function called before validating input data and filling attributes
     * @param array $attributes
     */
    protected function initBefore(array $attributes)
    {
        $regex = "regex:/\b" . config('common.base_query_table_placeholder'). "\b/";

        /* Apply regex validation for "query" attribute */
        $this->rules[self::QUERY] .= "|$regex";
    }


    /**
     * Get tables
     * @return array
     */
    public function getTables(): array
    {
        return $this->attributes[self::TABLES];
    }

    /**
     * Get base query
     * @return string
     */
    public function getBaseQuery(): string
    {
        return $this->attributes[self::QUERY];
    }

    /**
     * Get group columns
     * @return array
     */
    public function getGroupColumns(): array
    {
        return $this->attributes[self::GROUP_COLUMNS];
    }

    /**
     * Get table placeholder from config
     * @return string
     */
    public function getTablePlaceholder(): string
    {
        /* Get table placeholder */
        return config('common.base_query_table_placeholder');
    }

    /**
     * Function used to retrieve base query with given table name
     * @param string $table
     * @return string
     */
    public function injectTableIntoQuery(string $table): string
    {
        /* Get table placeholder */
        $tablePlaceholder = $this->getTablePlaceholder();

        return str_replace($tablePlaceholder, $table, $this->getBaseQuery());
    }
}