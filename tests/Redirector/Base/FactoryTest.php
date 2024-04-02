<?php

namespace Ixolit\Dislo\Redirector\Base;

use Ixolit\Dislo\Redirector\Rules\Actions\NoRedirection;
use Ixolit\Dislo\Redirector\Rules\Conditions\CountryCheck;
use Ixolit\Dislo\Redirector\Rules\Conditions\UrlCheck;
use Ixolit\Dislo\TestCase;

final class FactoryTest extends TestCase
{
    private function getFactory()
    {
        return new Factory();
    }

    public function getCreateActionFromArrayDataProvider()
    {
        return [
            'with existing action' => [
                'type' => 'NoRedirection',
                'data' => [],
                'actionClass' => NoRedirection::class,
            ],
            'with non-existing action' => [
                'type' => 'NotExistingAction',
                'data' => [],
                'actionClass' => null,
            ],
        ];
    }

    /**
     * @dataProvider getCreateActionFromArrayDataProvider
     */
    public function testCreateActionFromArray($type, $data, $actionClass)
    {
        $action = $this->getFactory()->createActionFromArray([
            'type' => $type,
            'data' => $data,
        ]);

        if (\is_null($actionClass)) {
            $this->assertNull($action);
        } else {
            $this->assertInstanceOf($actionClass, $action);
        }
    }

    public function getCreateConditionFromArrayDataProvider()
    {
        return [
            'with existing condition' => [
                'type' => 'UrlCheck',
                'data' => [
                    'comparator' => 'equals',
                    'value' => 'https://ixopay.com',
                ],
                'conditionClass' => UrlCheck::class,
            ],
            'with non-existing condition' => [
                'type' => 'NotExistingCondition',
                'data' => [],
                'conditionClass' => null,
            ],
        ];
    }

    /**
     * @dataProvider getCreateConditionFromArrayDataProvider
     */
    public function testCreateConditionFromArray($type, $data, $conditionClass)
    {
        $condition = $this->getFactory()->createConditionFromArray([
            'type' => $type,
            'data' => $data,
        ]);

        if (\is_null($conditionClass)) {
            $this->assertNull($condition);
        } else {
            $this->assertInstanceOf($conditionClass, $condition);
        }
    }
}
