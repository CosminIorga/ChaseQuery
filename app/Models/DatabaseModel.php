<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 18:09
 */

namespace App\Models;


use App\Exceptions\DatabaseModelException;

class DatabaseModel extends NonPersistentModel
{
    /**
     * Model attributes
     */
    const DATABASE_ID = "id";
    const DATABASE_LOAD = "load";
    const DATABASE_FINAL_LOAD = "final_load";
    const DATABASE_HOST = "host";
    const DATABASE_PORT = "port";
    const DATABASE_USERNAME = "username";
    const DATABASE_PASSWORD = "password";
    const DATABASE_CHARSET = "charset";
    const DATABASE_COLLATION = "collation";
    const DATABASE_PREFIX = "prefix";
    const DATABASE_STRICT_MODE = "strict";
    const DATABASE_ENGINE = "engine";

    /**
     * Allowed attributes
     */
    const ALLOWED_ATTRIBUTES = [
        self::DATABASE_ID,
        self::DATABASE_LOAD,
        self::DATABASE_FINAL_LOAD,
        self::DATABASE_HOST,
        self::DATABASE_PORT,
        self::DATABASE_USERNAME,
        self::DATABASE_PASSWORD,
        self::DATABASE_CHARSET,
        self::DATABASE_COLLATION,
        self::DATABASE_PREFIX,
        self::DATABASE_STRICT_MODE,
        self::DATABASE_ENGINE
    ];

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::DATABASE_ID => 'required',
        self::DATABASE_HOST => 'required|string',
        self::DATABASE_PORT => 'required|integer',
        self::DATABASE_USERNAME => 'required|string',
        self::DATABASE_PASSWORD => 'present|string'
    ];

    /**
     * Default attribute values
     * @var array
     */
    protected $defaultAttributeValues = [
        self::DATABASE_LOAD => 0,
        self::DATABASE_FINAL_LOAD => 0
    ];

    /**
     * DatabaseModel constructor.
     * @param array $databaseInformation
     */
    public function __construct(array $databaseInformation)
    {
        $this->preProcessDatabaseInformation($databaseInformation);

        $attributes = $this->getDatabaseWithId($databaseInformation[self::DATABASE_ID]);

        $attributes = $this->mergeAttributes($attributes, $databaseInformation);

        $attributes = $this->postProcessAttributes($attributes);

        parent::__construct($attributes);
    }

    /**
     * Pre-validation for received database information
     * @param array $databaseInformation
     * @throws DatabaseModelException
     */
    protected function preProcessDatabaseInformation(array $databaseInformation)
    {
        /* Check if database information contains ID key */
        if (!array_key_exists(self::DATABASE_ID, $databaseInformation)) {
            throw new DatabaseModelException(DatabaseModelException::MISSING_DATABASE_ID_KEY);
        }
    }

    /**
     * Function used to retrieve database database information given a databaseId
     * @param int $databaseId
     * @return array
     * @throws DatabaseModelException
     */
    protected function getDatabaseWithId(int $databaseId): array
    {
        $allDatabaseConfigs = config('database.connections');

        $attributes = [];
        array_walk($allDatabaseConfigs, function ($databaseConfig) use (&$attributes, $databaseId) {
            if (!array_key_exists(self::DATABASE_ID, $databaseConfig)) {
                return;
            }

            if ($databaseId == $databaseConfig[self::DATABASE_ID]) {
                $attributes = $databaseConfig;
            }
        });

        if (empty($attributes)) {
            throw new DatabaseModelException(DatabaseModelException::NO_DATABASE_WITH_GIVEN_ID_EXISTS);
        }

        return $attributes;
    }

    /**
     * Function used to merge config database information with volatile database information
     * @param array $attributes
     * @param array $databaseInformation
     * @return array
     */
    protected function mergeAttributes(array $attributes, array $databaseInformation): array
    {
        $attributes = array_merge($attributes, $databaseInformation);

        /* Intersect attribute keys with allowed attributes */
        $attributes = array_intersect_key($attributes, array_flip(self::ALLOWED_ATTRIBUTES));

        return $attributes;
    }

    /**
     * Function used to process one more final time the attributes before saving the model
     * @param array $attributes
     * @return array
     */
    protected function postProcessAttributes(array $attributes): array
    {
        /* Set final load to current load */
        if (array_key_exists(self::DATABASE_LOAD, $attributes) && !is_null($attributes[self::DATABASE_LOAD])) {
            $attributes[self::DATABASE_FINAL_LOAD] = $attributes[self::DATABASE_LOAD];
        }

        return $attributes;
    }

    /**
     * Function used to add additional load value to a database
     * @param float $load
     */
    public function addLoad(float $load)
    {
        $this->attributes[self::DATABASE_FINAL_LOAD] += $load;
    }

    /**
     * Function used to return the final load value of a database
     * @return float
     */
    public function getLoad(): float
    {
        return $this->attributes[self::DATABASE_FINAL_LOAD];
    }
}