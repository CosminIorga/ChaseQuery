<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 15/03/17
 * Time: 13:51
 */

namespace App\Models;


use App\Definitions\Worker;
use App\Interfaces\Payload;

class MapperPayloadModel extends NonPersistentModel implements Payload
{

    /**
     * Variables
     */
    const PAYLOAD_QUERY = "payload_query";
    const PAYLOAD_DATABASE_INFO = "payload_database_info";

    protected $rules = [
        self::PAYLOAD_QUERY => 'required|string'
    ];

    /**
     * MapperPayloadModel constructor.
     * @param DatabaseModel $databaseModel
     * @param string $query
     */
    public function __construct(
        DatabaseModel $databaseModel,
        string $query
    ) {
        $attributes = [
            self::PAYLOAD_QUERY => $query,
            self::PAYLOAD_DATABASE_INFO => $databaseModel
        ];

        parent::__construct($attributes);
    }

    /**
     * Function used to retrieve the Mapper function
     * @return string
     */
    public function getFunction(): string
    {
        return Worker::WORKER_MAP;
    }

    /**
     * Function implemented by both Mapper and Reducer Payloads
     * @return string
     */
    public function __toString(): string
    {
        return serialize($this);
    }
}