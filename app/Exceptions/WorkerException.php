<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/02/17
 * Time: 17:43
 */

namespace App\Exceptions;


use App\Interfaces\DefaultException;

class WorkerException extends \Exception implements DefaultException
{


    public function report()
    {
        //TODO: add logging
    }
}