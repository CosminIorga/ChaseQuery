<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 18:09
 */

namespace Models;


use App\Models\NonPersistentModel;
use Exceptions\ConnectionModelException;

class DatabaseConnectionModel extends NonPersistentModel
{
    /**
     * Model attributes
     */
    const CONNECTION_ID = "id";
    const CONNECTION_LOAD = "load";
    const CONNECTION_HOST = "host";
    const CONNECTION_PORT = "port";
    const CONNECTION_USERNAME = "username";
    const CONNECTION_PASSWORD = "password";
    const CONNECTION_CHARSET = "charset";
    const CONNECTION_COLLATION = "collation";
    const CONNECTION_PREFIX = "prefix";
    const CONNECTION_STRICT_MODE = "strict";
    const CONNECTION_ENGINE = "engine";

    /**
     * Allowed attributes
     */
    const ALLOWED_ATTRIBUTES = [
        self::CONNECTION_ID,
        self::CONNECTION_LOAD,
        self::CONNECTION_HOST,
        self::CONNECTION_PORT,
        self::CONNECTION_USERNAME,
        self::CONNECTION_PASSWORD,
        self::CONNECTION_CHARSET,
        self::CONNECTION_COLLATION,
        self::CONNECTION_PREFIX,
        self::CONNECTION_STRICT_MODE,
        self::CONNECTION_ENGINE
    ];

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::CONNECTION_ID => 'required',
        self::CONNECTION_HOST => 'required|string',
        self::CONNECTION_PORT => 'required|integer',
        self::CONNECTION_USERNAME => 'required|string',
        self::CONNECTION_PASSWORD => 'present|string'
    ];

    /**
     * DatabaseConnectionModel constructor.
     * @param array $connectionInformation
     */
    public function __construct(array $connectionInformation)
    {
        $this->preProcessConnectionInformation($connectionInformation);

        $attributes = $this->getConnectionWithId($connectionInformation[self::CONNECTION_ID]);

        $attributes = $this->mergeAttributes($attributes, $connectionInformation);

        parent::__construct($attributes);
    }

    /**
     * Pre-validation for received connection information
     * @param array $connectionInformation
     * @throws ConnectionModelException
     */
    protected function preProcessConnectionInformation(array $connectionInformation)
    {
        /* Check if connection information contains ID key */
        if (!array_key_exists(self::CONNECTION_ID, $connectionInformation)) {
            throw new ConnectionModelException(ConnectionModelException::MISSING_CONNECTION_ID_KEY);
        }
    }

    /**
     * Function used to retrieve database connection information given a connectionId
     * @param int $connectionId
     * @return array
     * @throws ConnectionModelException
     */
    protected function getConnectionWithId(int $connectionId): array
    {
        $allDatabaseConfigs = config('database.connections');

        $attributes = [];
        array_walk($allDatabaseConfigs, function ($databaseConfig) use (&$attributes, $connectionId) {
            if ($connectionId == $databaseConfig[self::CONNECTION_ID]) {
                $attributes = $databaseConfig;
            }
        });

        if (empty($attributes)) {
            throw new ConnectionModelException(ConnectionModelException::NO_CONNECTION_WITH_GIVEN_ID);
        }

        return $attributes;
    }

    /**
     * Function used to merge config connection information with volatile connection information
     * @param array $attributes
     * @param array $connectionInformation
     * @return array
     */
    protected function mergeAttributes(array $attributes, array $connectionInformation)
    {
        $attributes = array_merge($attributes, $connectionInformation);

        /* Intersect attribute keys with allowed attributes */
        $attributes = array_intersect_key($attributes, array_flip(self::ALLOWED_ATTRIBUTES));

        return $attributes;
    }
}