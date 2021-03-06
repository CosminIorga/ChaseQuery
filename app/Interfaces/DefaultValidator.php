<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 14:11
 */

namespace App\Interfaces;

use App\Models\ResponseModel;

/**
 * Interface DefaultValidator
 * @package Validators
 * This interface should be extended by all custom validators
 */
interface DefaultValidator
{
    /**
     * Function called to validate data
     * @return ResponseModel
     */
    public function validate(): ResponseModel;
}