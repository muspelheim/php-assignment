<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostNumberPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var array
     */
    private array $totalPostsByUser = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $monthKey = $postTo->getDate()?->format('M, Y');
        $authorId = $postTo->getAuthorId();

        if (!isset($this->totalPostsByUser[$monthKey][$authorId])) {
            $this->totalPostsByUser[$monthKey][$authorId] = 0;
        }

        $this->totalPostsByUser[$monthKey][$authorId]++;
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $statisticsTo = new StatisticsTo();
        foreach ($this->totalPostsByUser as $monthPeriod => $usersTotalPosts) {
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($monthPeriod)
                ->setValue(round(array_sum($usersTotalPosts) / count($usersTotalPosts), 2))
                ->setUnits(self::UNITS);

            $statisticsTo->addChild($child);
        }

        return $statisticsTo;
    }
}
