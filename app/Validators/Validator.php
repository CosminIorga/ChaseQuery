<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 13:23
 */

namespace App\Validators;

use App\Definitions\Validator as ValidatorDefinitions;
use App\Exceptions\ValidatorException;
use App\Models\ValidatorResponseModel;

/**
 * Class Validator
 * @package Jobs
 * Used to write custom validators
 */
class Validator
{
    /**
     * Available validators
     * @var array
     */
    protected static $validators = [
        ArrayStructureValidator::class
    ];


    /**
     * Function called to validate $data against the $validator class
     * @param array $data
     * @param string $validator
     * @return ValidatorResponseModel
     * @throws ValidatorException
     */
    public static function validate($data, $validator): ValidatorResponseModel
    {
        /* Call validation on */
        if (!in_array($validator, self::$validators)) {
            throw new ValidatorException(ValidatorDefinitions::INVALID_VALIDATOR_CLASS_GIVEN);
        }

        /* Call validator class constructor with arguments based on $data array */
        $validatorInstance = new $validator(...$data);

        if (!method_exists($validatorInstance, ValidatorDefinitions::FUNCTION_CALLED_TO_VALIDATE_DATA)) {
            throw new ValidatorException(ValidatorDefinitions::VALIDATOR_DOES_NOT_HAVE_VALIDATE_FUNCTION);
        }

        return $validatorInstance->{ValidatorDefinitions::FUNCTION_CALLED_TO_VALIDATE_DATA}();
    }
}