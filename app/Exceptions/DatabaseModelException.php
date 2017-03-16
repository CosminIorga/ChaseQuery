<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 18:39
 */

namespace App\Exceptions;


use App\Interfaces\DefaultException;
use Exception;

class DatabaseModelException extends Exception implements DefaultException
{
    const MISSING_DATABASE_ID_KEY = "Missing database 'id' key";
    const NO_DATABASE_WITH_GIVEN_ID_EXISTS = "No database with given 'id' exists ";

    public function report()
    {
        // TODO: Implement report() method.
    }
}