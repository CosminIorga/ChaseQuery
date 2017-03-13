<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 10/03/17
 * Time: 18:39
 */

namespace Exceptions;


use App\Interfaces\DefaultException;
use Exception;

class ConnectionModelException extends Exception implements DefaultException
{
    const MISSING_CONNECTION_ID_KEY = "Missing connection 'id' key";
    const NO_CONNECTION_WITH_GIVEN_ID = "No connection with given 'id' exists ";

    public function report()
    {
        // TODO: Implement report() method.
    }
}