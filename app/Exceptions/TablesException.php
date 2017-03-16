<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 14:15
 */

namespace App\Exceptions;


use App\Interfaces\DefaultException;

class TablesException extends \Exception implements DefaultException
{
    const TABLE_DOES_NOT_EXIST = "One or more given table(s) do not exist";


    public function report()
    {
        // TODO: Implement report() method.
    }
}