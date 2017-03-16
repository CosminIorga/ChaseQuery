<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 15/03/17
 * Time: 17:59
 */

namespace App\Interfaces;

/**
 * Interface Payload
 * @package App\Interfaces
 */
interface Payload
{
    /**
     * Function implemented by both Mapper and Reducer Payloads
     * @return string
     */
    public function getFunction(): string;

    /**
     * Function implemented by both Mapper and Reducer Payloads
     * @return string
     */
    public function __toString(): string;
}