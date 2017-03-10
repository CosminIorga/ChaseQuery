<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 13:51
 */

namespace App\Definitions;

/**
 * Class Validator
 * @package Definitions
 */
final class Validator
{

    # Function called to validate data
    const FUNCTION_CALLED_TO_VALIDATE_DATA = "validate";

    # Validator exception message
    const INVALID_VALIDATOR_CLASS_GIVEN = "Invalid validator class given ";
    const VALIDATOR_DOES_NOT_HAVE_VALIDATE_FUNCTION =
        "Method '" .
        self:: FUNCTION_CALLED_TO_VALIDATE_DATA .
        "' does not exist in validator";
    const FIELD_NOT_ALLOWED = "Invalid field in array structure";
    const VALIDATOR_TYPE_NOT_FOUND = "Invalid validation type received";
    const VALIDATION_FAILED_FOR_FIELD = "Validation failed for value '%s'. Must be of type '%s' ";
}