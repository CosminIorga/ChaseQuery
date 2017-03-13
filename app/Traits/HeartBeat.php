<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 13:01
 */

namespace Traits;


trait HeartBeat
{
    /**
     * Variable used to store available connections
     * @var array
     */
    protected $connections = [];

    /**
     * Function used to retrieve connection load
     * @param string $connectionId
     * @return float
     */
    public final function getConnectionLoad(string $connectionId): float
    {
        if (empty($this->connections)) {
            $this->getAvailableConnections();
        }

        $connection = array_filter($this->connections, function ($connection) use ($connectionId) {
            return $connection['id'] == $connectionId;
        });

        if (empty($connection)) {
            return null;
        }

        return current($connection)['load'];
    }

    /**
     * Function used to retrieve available connections
     * @return array
     */
    public final function getAvailableConnections(): array
    {
        $processSections = true;
        $connections = parse_ini_file($this->getConnectionsFile(), $processSections);

        $this->refreshConnectionsArray($connections);

        return $connections;
    }

    /**
     * Function used to retrieve "Heartbeat.vlt.ini" full file path
     * @return string
     */
    private final function getConnectionsFile(): string
    {
        $connectionsFileRelativeToStoragePath = "volatile/Heartbeat.vlt.ini";

        $connectionsFileFullPath = storage_path($connectionsFileRelativeToStoragePath);

        return $connectionsFileFullPath;
    }

    /**
     * Internal function used to set class-wide $connections variable
     * @param $connections
     */
    private final function refreshConnectionsArray($connections)
    {
        $this->connections = $connections;
    }
}