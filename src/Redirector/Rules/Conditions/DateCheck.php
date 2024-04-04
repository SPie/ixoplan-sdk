<?php

namespace Ixolit\Dislo\Redirector\Rules\Conditions;

use Ixolit\Dislo\Redirector\Base\RedirectorRequestInterface;
use Ixolit\Dislo\Redirector\Base\RedirectorResultInterface;
use Ixolit\Dislo\Redirector\Base\RedirectorStateInterface;

final class DateCheck extends Condition
{
    protected function getPossibleComparatorOperators()
    {
        return [
            DateCheck::COMPARATOR_GREATER_THAN,
            DateCheck::COMPARATOR_GREATER_THAN_EQUALS,
            DateCheck::COMPARATOR_LOWER_THAN,
            DateCheck::COMPARATOR_LOWER_THAN_EQUALS,
            DateCheck::COMPARATOR_EQUALS,
        ];
    }

    public function evaluateFromRequest(
        RedirectorStateInterface $redirectorState,
        RedirectorRequestInterface $request,
        RedirectorResultInterface $result
    ) {
        $date = new \DateTime($request->getHeaders()['date'] ?: null);
        $value = new \DateTime($this->parameters['value']);

        switch ($this->parameters['comparator']) {
            case self::COMPARATOR_GREATER_THAN:
                return $date > $value;
            case self::COMPARATOR_GREATER_THAN_EQUALS:
                return $date >= $value;
            case self::COMPARATOR_LOWER_THAN:
                return $date < $value;
            case self::COMPARATOR_LOWER_THAN_EQUALS:
                return $date <= $value;
            case self::COMPARATOR_EQUALS:
                return $date == $value;
        }
    }
}
