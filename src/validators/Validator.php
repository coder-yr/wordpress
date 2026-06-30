<?php

namespace ClinicManagement\Validators;

class Validator
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Create a new validator instance.
     *
     * @param array $data
     * @param array $rules
     * @return static
     */
    public static function make(array $data, array $rules)
    {
        return new static($data, $rules);
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Validate a specific rule against a value.
     *
     * @param string $field
     * @param mixed $value
     * @param string $rule
     */
    protected function validateRule(string $field, $value, string $rule)
    {
        // Example basic rules: required, numeric, date, time
        if ($rule === 'required' && empty($value)) {
            $this->addError($field, "The {$field} field is required.");
        }

        if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
            $this->addError($field, "The {$field} must be a number.");
        }

        if ($rule === 'date' && !empty($value) && !strtotime($value)) {
            $this->addError($field, "The {$field} is not a valid date.");
        }
        
        if (strpos($rule, 'min:') === 0 && !empty($value)) {
            $min = (int) substr($rule, 4);
            if (is_numeric($value) && $value < $min) {
                $this->addError($field, "The {$field} must be at least {$min}.");
            }
        }
    }

    protected function addError(string $field, string $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
}
