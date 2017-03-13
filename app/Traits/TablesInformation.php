<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 13:57
 */

namespace Traits;


use Exceptions\TablesException;

trait TablesInformation
{
    /**
     * Function used to retrieve information regarding given tables
     * @param array $tables
     * @return array
     * @throws TablesException
     */
    public function getTablesInformation(array $tables): array
    {
        $processSections = true;
        $allTablesInformation = parse_ini_file($this->getTablesFile(), $processSections);

        $tablesInformation = array_intersect_key($allTablesInformation, array_flip($tables));

        if (array_keys($tablesInformation) != array_keys($tables)) {
            throw new TablesException(TablesException::TABLE_DOES_NOT_EXIST);
        }

        return $tablesInformation;
    }


    /**
     * Internal function used to retrieve "Tables.vlt.ini" fie path
     * @return string
     */
    private final function getTablesFile(): string
    {
        $tablesFileRelativeToStoragePath = "volatile/Tables.vlt.ini";

        $tablesFileFullPath = storage_path($tablesFileRelativeToStoragePath);

        return $tablesFileFullPath;
    }
}