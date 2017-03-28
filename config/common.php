<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 15/03/17
 * Time: 16:52
 */

/**
 * File used to store common configuration information
 */
return [
    #Placeholder for table when sending query
    'base_query_table_placeholder' => '_TABLE_',
    #Variable used to decide if a partitioned table sent to one DB node
    # should be further split into subsequent sub-queries for each partition
    'force_splitting_by_partitions' => true,
    #If force_splitting_by_partitions params is enabled, then this value should indicate
    # the number of maximum partitions it may assign to one query
    'partition_granularity' => 1
];