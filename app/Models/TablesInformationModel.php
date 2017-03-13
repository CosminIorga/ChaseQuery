<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/03/17
 * Time: 13:02
 */

namespace Models;


use App\Models\NonPersistentModel;

class TablesInformationModel extends NonPersistentModel
{
    /**
     * Model attributes
     */
    const TABLE_NAME = "table_name";
    const TABLE_IS_PARTITIONED = "table_is_partitioned";
    const TABLE_PARTITIONS = "table_partitions";
    const TABLE_PARTITION_COUNT = "table_partition_count";

    /**
     * Model allowed attributes
     */
    const ALLOWED_FIELDS = [
        self::TABLE_NAME,
        self::TABLE_IS_PARTITIONED,
        self::TABLE_PARTITIONS,
        self::TABLE_PARTITION_COUNT
    ];

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        self::TABLE_NAME => 'required|string',
        self::TABLE_IS_PARTITIONED => 'required|boolean',
        self::TABLE_PARTITIONS => 'array|nullable',
        self::TABLE_PARTITION_COUNT => 'integer'
    ];
}