<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/02/17
 * Time: 13:45
 */

namespace App\Validators;

use App\Definitions\Validator;
use App\Exceptions\ValidatorException;
use App\Interfaces\DefaultValidator;
use Illuminate\Support\Collection;
use App\Models\ValidatorResponseModel;

/**
 * Class ArrayStructureValidator
 * @package App\Validators
 * Used to validate an array structure and elements type
 */
class ArrayStructureValidator implements DefaultValidator
{
    /* Constants used in building the imposed array structure */
    public const NUMERIC_KEY = "numericKey";
    public const STRING_TYPE = "string";
    public const ARRAY_TYPE = "array";
    public const INTEGER_TYPE = "int";
    public const FLOAT_TYPE = "bool";

    /**
     * Array structure
     * @var array
     */
    private $fields;

    /**
     * Data to validate
     * @var Collection
     */
    private $dataToValidate;

    /**
     * CQueryValidator constructor.
     * @param array $dataToValidate
     * @param array $fields
     */
    public function __construct(array $dataToValidate, array $fields)
    {
        $this->dataToValidate = $dataToValidate;
        $this->fields = $fields;
    }

    /**
     * Function called to validate data
     * @return ValidatorResponseModel
     */
    public function validate(): ValidatorResponseModel
    {
        try {
            array_walk($this->dataToValidate, function ($item, $key) {
                $this->deepen($item, $key, $this->fields);
            });

        } catch (ValidatorException $exception) {
            return (new ValidatorResponseModel())
                ->failure()
                ->setMessage($exception->getMessage());
        }

        return (new ValidatorResponseModel())
            ->success();
    }

    /**
     * Function called to validate multi-dimensional arrays. Called recursively for each array found
     * @param mixed $value
     * @param string $key
     * @param array $currentFields
     * @throws ValidatorException
     */
    private function deepen($value, $key, $currentFields)
    {
        $computedKey = $key;

        /* Overwrite computeKey is key is numeric */
        if (is_numeric($key)) {
            $computedKey = self::NUMERIC_KEY;
        }

        if (array_key_exists($computedKey, $currentFields)) {
            $this->checkValue($value, $currentFields[$computedKey]);
        } else {
            /* Throw exception if key is neither numeric nor appears in allowed fields */
            throw new ValidatorException(Validator::FIELD_NOT_ALLOWED);
        }

        /* Check if value is array. Go deeper if so */
        if (is_array($value)) {
            $currentFields = $currentFields[$computedKey];
            array_walk($value, function ($item, $key) use ($currentFields) {
                $this->deepen($item, $key, $currentFields);
            });
        }
    }

    /**
     * Function used to check if $value is of $type
     * @param mixed $value
     * @param string $type
     * @throws ValidatorException
     */
    private function checkValue($value, $type)
    {
        switch (true) {
            case $type == self::STRING_TYPE:
                $isValid = is_string($value);
                break;
            case $type == self::INTEGER_TYPE:
                $isValid = is_int($value);
                break;
            case $type == self::FLOAT_TYPE:
                $isValid = is_bool($value);
                break;
            case is_array($type):
                $isValid = is_array($value);
                break;
            default:
                /* It should not reach this branch if code is written properly */
                throw new ValidatorException(Validator::VALIDATOR_TYPE_NOT_FOUND);
        }

        if (!$isValid) {
            throw new ValidatorException(
                sprintf(
                    Validator::VALIDATION_FAILED_FOR_FIELD,
                    $value,
                    $type
                )
            );
        }
    }

}