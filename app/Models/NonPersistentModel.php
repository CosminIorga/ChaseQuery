<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 01/03/17
 * Time: 17:17
 */

namespace App\Models;


use App\Traits\ModelValidator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class NonPersistentModel
 * @package App\Models
 */
class NonPersistentModel implements Arrayable, Jsonable
{
    use ModelValidator;

    /**
     * Variable used to store model's attributes
     * @var array
     */
    protected $attributes = [];

    /**
     * Variable used to store default values for attributes
     * @var array
     */
    protected $defaultAttributeValues = [];

    /**
     * NonPersistentModel constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        array_walk($this->defaultAttributeValues, function ($attribute, $defaultValue) use (&$attributes) {
            if (!array_key_exists($attribute, $attributes)) {
                $attributes[$attribute] = $defaultValue;
            }
        });

        $this->initBefore($attributes);

        $this->validate($attributes);

        $this->fill($attributes);

        $this->initAfter($attributes);
    }

    /**
     * Function called before validating input data and filling attributes
     * @param array $attributes
     */
    protected function initBefore(array $attributes)
    {
    }

    /**
     * Function called after validating input data and filling attributes
     * @param array $attributes
     */
    protected function initAfter(array $attributes)
    {
    }

    /**
     * Fill the model with an array of attributes
     * @param array $attributes
     * @return NonPersistentModel
     */
    public function fill(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set model attribute to given value
     * @param string $attribute
     * @param mixed $value
     * @return NonPersistentModel
     */
    public function setAttribute(string $attribute, $value): self
    {
        $this->attributes[$attribute] = $value;

        $this->validate($this->getAttributes());

        return $this;
    }

    /**
     * Set multiple model attributes
     * @param array $attributes
     * @return NonPersistentModel
     */
    public function setAttributes(array $attributes): self
    {
        array_walk($attributes, function ($value, $attribute) {
            $this->setAttribute($attribute, $value);
        });

        $this->validate($this->getAttributes());

        return $this;
    }

    /**
     * Get model attribute
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Get model attributes
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        /* Iterate over attributes (keys) */
        array_walk(array_keys($this->getAttributes()), function ($attribute) use (&$array) {
            $value = $this->getAttribute($attribute);

            if (is_object($value)) {
                if (method_exists($value, 'toArray') || $value instanceof Arrayable) {
                    $array[$attribute] = $value->toArray();
                    return;
                }

                $array[$attribute] = (array)$value;
                return;
            }

            $array[$attribute] = $value;
        });

        return $array;
    }

    /**
     * Convert the object to its JSON representation.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }
}