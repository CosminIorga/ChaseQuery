<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/03/17
 * Time: 15:37
 */

namespace App\Repositories;


use DB;

class CommonRepository
{

    /**
     * @param string $query
     * @return string
     */
    public function getUsedPartitionsForQuery(string $query): string
    {
        $query = "EXPLAIN EXTENDED $query";

        $result = DB::select(DB::raw($query));

        return (current($result))->partitions ?? "";
    }
}