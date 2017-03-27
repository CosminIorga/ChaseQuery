<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/03/17
 * Time: 13:18
 */

namespace App\Models;


use App\Definitions\Worker;
use App\Interfaces\Payload;

class ReducerPayloadModel extends NonPersistentModel implements Payload
{
    /**
     * Model attributes
     */
    const PAYLOAD_MAPPED_DATA = "payload_mapped_data";
    const GROUP_COLUMNS = "group_columns";

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::PAYLOAD_MAPPED_DATA => 'required|array'
    ];

    /**
     * ReducerPayloadModel constructor.
     * @param array $mappedData
     * @param array $groupColumns
     */
    public function __construct(array $mappedData, array $groupColumns)
    {
        $attributes = [
            self::PAYLOAD_MAPPED_DATA => $mappedData,
            self::GROUP_COLUMNS => $groupColumns
        ];

        parent::__construct($attributes);
    }

    /**
     * Function implemented by both Mapper and Reducer Payloads
     * @return string
     */
    public function getFunction(): string
    {
        return Worker::WORKER_REDUCE;
    }

    /**
     * Function implemented by both Mapper and Reducer Payloads
     * @return string
     */
    public function __toString(): string
    {
        return serialize($this);
    }

    /**
     * Getter for mapped data
     * @return array
     */
    public function getMappedData(): array
    {
        return $this->getAttribute(self::PAYLOAD_MAPPED_DATA);
    }

    /**
     * Getter for group columns
     * @return array
     */
    public function getGroupColumns(): array
    {
        return $this->getAttribute(self::GROUP_COLUMNS);
    }
}