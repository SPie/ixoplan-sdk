<?php

namespace Ixolit\Dislo\Redirector\Rules\Conditions;

use Ixolit\Dislo\Redirector\Base\RedirectorRequestInterface;
use Ixolit\Dislo\Redirector\Base\RedirectorResultInterface;
use Ixolit\Dislo\Redirector\Base\RedirectorStateInterface;
use Ixolit\Dislo\TestCase;
use Mockery;

final class DateCheckTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @param array $parameters
     * @return DateCheck
     */
    private function getDateCheck($parameters = [])
    {
        return new DateCheck($parameters);
    }

    /**
     * @return aray
     */
    public function getSetParametersDataProvider()
    {
        $date = '1970-01-01T13:37';

        return [
            'with greater than comparator' => [
                'data' => [
                    'comparator' => 'gt',
                    'value' => $date,
                ],
                'parameters' => [
                    'comparator' => 'gt',
                    'value' => $date,
                ],
            ],
            'with greater than equals comparator' => [
                'data' => [
                    'comparator' => 'gte',
                    'value' => $date,
                ],
                'parameters' => [
                    'comparator' => 'gte',
                    'value' => $date,
                ],
            ],
            'with lower than comparator' => [
                'data' => [
                    'comparator' => 'lt',
                    'value' => $date,
                ],
                'parameters' => [
                    'comparator' => 'lt',
                    'value' => $date,
                ],
            ],
            'with lower than equals comparator' => [
                'data' => [
                    'comparator' => 'lte',
                    'value' => $date,
                ],
                'parameters' => [
                    'comparator' => 'lte',
                    'value' => $date,
                ],
            ],
            'with equals comparator' => [
                'data' => [
                    'comparator' => 'equals',
                    'value' => $date,
                ],
                'parameters' => [
                    'comparator' => 'equals',
                    'value' => $date,
                ],
            ],
            'with invalid comparator' => [
                'data' => [
                    'comparator' => 'invalid comparator',
                    'value' => $date,
                ],
                'parameters' => [],
                'validationException' => true,
            ],
            'with unexpected data' => [
                'data' => [
                    'comparator' => 'equals',
                    'value' => $date,
                    'unexpected key' => 'unexpected value',
                ],
                'parameters' => [
                    'comparator' => 'equals',
                    'value' => $date,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getSetParametersDataProvider
     *
     * @param array $data
     * @param array $parameters
     * @param bool $validationException
     */
    public function testSetParameters($data, $parameters, $validationException = false)
    {
        $dateCheck = $this->getDateCheck();

        try {
            $this->assertEquals(
                $this->getDateCheck($parameters),
                $this->getDateCheck()->setParameters($data)
            );

            if ($validationException) {
                $this->assertTrue(false, 'Validation Exception was expected');
            }
        } catch(\Exception $e) {
            if (!$validationException) {
                throw $e;
            }

            $this->assertTrue(true);
        }
    }

    /**
     * @return array
     */
    public function getEvaluateFromRequestDataProvider()
    {
        $dateHeader = 'Tue, 26 Mar 2024 09:52:00 UTC';

        return [
            'with greater than comparator and greated date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-25T00:00',
                'comparator' => 'gt',
                'result' => true,
            ],
            'with greater than comparator and lower date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-27T00:00',
                'comparator' => 'gt',
                'result' => false,
            ],
            'with lower than comparator and lower date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-27T00:00',
                'comparator' => 'lt',
                'result' => true,
            ],
            'with lower than comparator and greater date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-25T00:00',
                'comparator' => 'lt',
                'result' => false,
            ],
            'with equals comparator and equal date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-26T09:52',
                'comparator' => 'equals',
                'result' => true,
            ],
            'with equals comparator and lower date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-25T00:00',
                'comparator' => 'equals',
                'result' => false,
            ],
            'with greater than equals comparator and greated date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-25T00:00',
                'comparator' => 'gte',
                'result' => true,
            ],
            'with greater than equals comparator and equal date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-26T09:52',
                'comparator' => 'gte',
                'result' => true,
            ],
            'with greater than equals comparator and lower date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-27T00:00',
                'comparator' => 'gte',
                'result' => false,
            ],
            'with lower than equals comparator and lower date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-27T00:00',
                'comparator' => 'lte',
                'result' => true,
            ],
            'with lower than equals comparator and equal date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-26T09:52',
                'comparator' => 'lte',
                'result' => true,
            ],
            'with lower than equals comparator and greater date' => [
                'dateHeader' => $dateHeader,
                'value' => '2024-03-25T00:00',
                'comparator' => 'lte',
                'result' => false,
            ],
            'without date header' => [
                'dateHeader' => null,
                'value' => '2024-03-25T00:00',
                'comparator' => 'gt',
                'result' => true, // the current date time will be something greater than 2024-03-25
            ],
        ];
    }

    /**
     * @dataProvider getEvaluateFromRequestDataProvider
     *
     * @param string|null $dateHeader
     * @param string      $value
     * @param string      $comparator
     * @param bool        $result
     */
    public function testEvaluateFromRequest($dateHeader, $value, $comparator, $result)
    {
        $request = Mockery::spy(RedirectorRequestInterface::class);
        $request
            ->shouldReceive('getHeaders')
            ->andReturn(['date' => $dateHeader]);
        $dateCheck = $this->getDateCheck(['comparator' => $comparator, 'value' => $value]);

        $this->assertEquals(
            $result,
            $dateCheck->evaluateFromRequest(
                Mockery::spy(RedirectorStateInterface::class),
                $request,
                Mockery::spy(RedirectorResultInterface::class),
            )
        );
    }
}
