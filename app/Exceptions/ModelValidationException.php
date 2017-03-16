<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 09/03/17
 * Time: 16:03
 */

namespace App\Exceptions;


use App\Interfaces\DefaultException;

class ModelValidationException extends \Exception implements DefaultException
{
    public function report()
    {
        return;
    }
}