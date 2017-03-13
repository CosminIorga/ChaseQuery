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

    const CONNECTION_ID = "id";
    const CONNECTION_LOAD = "load";
    //TODO: additional information from database config


    /**
     * DatabaseConnectionModel constructor.
     * @param array $connectionInformation
     */
    public function __construct(array $connectionInformation)
    {
        $this->preProcessConnectionInformation($connectionInformation);

        $attributes = $this->getConnectionWithId($connectionInformation);

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
     * @param array $connectionInformation
     * @return array
     */
    protected function getConnectionWithId(array $connectionInformation): array
    {
        //TODO: config(database.connections). Iterate and take only given id. Throw exception if ID not found

    }
}