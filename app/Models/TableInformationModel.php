<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/03/17
 * Time: 13:02
 */

namespace App\Models;


class TableInformationModel extends NonPersistentModel
{
    /**
     * Model attributes
     */
    const TABLE_NAME = "table_name";
    const TABLE_IS_PARTITIONED = "is_partitioned";
    const TABLE_PARTITIONS = "partitions";
    const USED_TABLE_PARTITIONS = "used_partitions";

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::TABLE_NAME => 'required|string',
        self::TABLE_IS_PARTITIONED => 'boolean',
        self::TABLE_PARTITIONS => 'array|nullable'
    ];


    /**
     * Function used to retrieve table name
     * @return mixed
     */
    public function getTableName(): string
    {
        return $this->attributes[self::TABLE_NAME];
    }

    /**
     * Function used to retrieve if table is partitioned
     * @return mixed
     */
    public function isPartitioned(): bool
    {
        return (bool) $this->attributes[self::TABLE_IS_PARTITIONED];
    }

    /**
     * Function used to retrieve table partitions
     * @return array
     */
    public function getTablePartitions(): array
    {
        return $this->attributes[self::TABLE_PARTITIONS] ?? [];
    }

    /**
     * Function used to retrieve used table partitions
     * @return array
     */
    public function getUsedTablePartitions(): array
    {
        return $this->attributes[self::USED_TABLE_PARTITIONS] ?? [];
    }

    /**
     * Function used to set used table partitions
     * @param array $usedPartitions
     * @return TableInformationModel
     */
    public function setUsedTablePartitions(array $usedPartitions): self
    {
        $this->setAttribute(self::USED_TABLE_PARTITIONS, $usedPartitions);

        return $this;
    }
}