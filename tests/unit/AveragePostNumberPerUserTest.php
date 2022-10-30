<?php

namespace Tests\unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AveragePostNumberPerUser;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

class AveragePostNumberPerUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider postDataProvider
     */
    public function testCalculate($expected, $paramsToRange, $posts): void
    {
        $calculator = new AveragePostNumberPerUser();
        $calculator->setParameters((new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER)
            ->setStartDate(new DateTime($paramsToRange['startDate']))
            ->setEndDate(new DateTime($paramsToRange['endDate'])));

        foreach ($posts as $post) {
            $calculator->accumulateData($post);
        }

        $value = $calculator->calculate();
        foreach ($expected as $index => $item) {
            $dataSet = $value->getChildren()[$index] ?? $value;

            $this->assertEquals($item, $dataSet->getValue());
        }
    }

    /**
     * @return array[]
     */
    public function postDataProvider(): array
    {
        return [
            'Empty post list' => [
                'averagePosts' => [
                    0
                ],
                'postToRange' =>[
                    'startDate' => '01-01-2022',
                    'endDate'   => '31-01-2022',
                ],
                'posts' => []
            ],
            'posts within 1 month list' => [
                'averagePosts' => [
                    1.5
                ],
                'postToRange' =>[
                    'startDate' => '01-01-2022',
                    'endDate'   => '31-01-2022',
                ],
                'posts' => [
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('20-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('20-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('10-01-2022')),
                ]
            ],
            'posts within 2 month list' => [
                'averagePosts' => [
                    1,
                    1.5
                ],
                'postToRange' => [
                    'startDate' => '01-01-2022',
                    'endDate'   => '31-02-2022',
                ],
                'posts' => [
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                ]
            ],
            'posts within 2 month different users in different time list' => [
                'averagePosts' => [
                    2,
                    3
                ],
                'postToRange' => [
                    'startDate' => '01-01-2022',
                    'endDate' => '31-02-2022',
                ],
                'posts' => [
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                ]
            ],
            'posts within 1 month (posts within 2 months)' => [
                'averagePosts' => [
                    1
                ],
                'postToRange' => [
                    'startDate' => '01-01-2022',
                    'endDate' => '31-01-2022',
                ],
                'posts' => [
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_1')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-01-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                    (new SocialPostTo())->setAuthorId('user_2')->setDate(new DateTime('01-02-2022')),
                ]
            ]
        ];
    }
}
