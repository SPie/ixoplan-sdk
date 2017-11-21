<?php

namespace Ixolit\Dislo\Redirector\Rules\Conditions;

use Ixolit\Dislo\Redirector\Base\NameValue;
use Ixolit\Dislo\Redirector\Base\RedirectorRequestInterface;
use Ixolit\Dislo\Redirector\Base\RedirectorResult;

/**
 * Class KeyValueCheck
 * @package Ixolit\Dislo\Redirector\Rules\Conditions
 */
abstract class NameValueCheck extends Condition {

    /**
     * @return string[]
     */
    protected function getPossibleComparatorOperators() {
        return array_merge(
            [
                Condition::COMPARATOR_EXISTS,
                Condition::COMPARATOR_NOT_EXISTS
            ],
            parent::getPossibleComparatorOperators()
        );
    }

    /**
     * @param array $parameterKeys
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getParameterByKey($parameterKeys, $key, $default = null) {
        return isset($parameterKeys[$key]) ? $this->parameters[$parameterKeys[$key]] : $default;
    }

    /**
     * @param RedirectorRequestInterface $request
     * @return NameValue[]
     */
    protected abstract function getNameValues(RedirectorRequestInterface $request);

    /**
     * Prepare a name for being used as array key, e.g. make it case insensitive
     *
     * @param string $name
     * @return mixed
     */
    protected function sanitizeName($name) {
        return $name;
    }

    /**
     * Prepare a value to be compared by comparators expecting strings
     *
     * @param mixed $value
     * @return string
     */
    protected function sanitizeValue($value) {

        // RFC2616: Multiple headers with same name MAY appear for fields defined as a comma-separated list, It MUST be
        // possible to concatenate them without changing the semantics of the message. Multiple cookies and query
        // parameters with same name may appear too, treat them all the same way and override if needed ...
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        else {
            $value = strval($value);
        }
        return $value;
    }

    /**
     * @param NameValue[] $nameValues
     * @return string[]
     */
    protected function sanitizeNameValues($nameValues) {

        $sanitized = [];

        foreach ($nameValues as $nameValue) {
            // TODO: check for existing key, convert to array?
            $sanitized[$this->sanitizeName($nameValue->getName())] = $this->sanitizeValue($nameValue->getValue());
        }

        return $sanitized;
    }

    /**
     * @param RedirectorRequestInterface $request
     * @param RedirectorResult $result
     * @return bool
     */
    public function evaluateFromRequest(RedirectorRequestInterface $request, RedirectorResult $result) {
        return $this->check($this->sanitizeNameValues($this->getNameValues($request)));
    }

    /**
     * @param string[] $keyValues
     * @return bool
     */
    public function check($keyValues) {

        $parameterKeys = $this->getParameterKeys();
        $comparator = $this->getParameterByKey($parameterKeys, self::KEY_PARAM_COMP);
        $paramName = $this->sanitizeName($this->getParameterByKey($parameterKeys, self::KEY_PARAM_NAME));
        $paramValue = $this->getParameterByKey($parameterKeys, self::KEY_PARAM_VALUE, '');

        if ($comparator === self::COMPARATOR_EXISTS) {
            return array_key_exists($paramName, $keyValues);
        }
        if ($comparator === self::COMPARATOR_NOT_EXISTS) {
            return !array_key_exists($paramName, $keyValues);
        }

        $value = isset($keyValues[$paramName]) ? $keyValues[$paramName] : null;

        return $this->compare($value, $paramValue, $comparator);
    }
}