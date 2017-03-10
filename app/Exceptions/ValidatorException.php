<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 13:50
 */

namespace App\Exceptions;


use App\Interfaces\DefaultException;

class ValidatorException extends \Exception implements DefaultException
{
    public function report()
    {
        return;
    }
}