<?php

namespace Volkhin\Pogazam\Calendar;

class Calendar
{
    const CALENDAR_BEGINS = '20150101';

    const NEXT_MONTH = 'next';
    const PREVIOUS_MONTH = 'previous';
    const CURRENT_DAY = 'current';

    const MONTH_MENU = 'months_menu';
    const DAY_MENU = 'days_menu';

    private $ends;
    private $begins;
    private $lastDay;

    private $oneMonthInterval;
    private $oneDayInterval;
    private $periodInterval;

    private $currentMonth = '';
    private $nextMonth = '';
    private $previousMonth = '';

    public function __construct(string $currentMonth = null)
    {
        $currentDate = new \DateTime();

        $this->begins = new \DateTime(self::CALENDAR_BEGINS);
        $this->ends = new \DateTime($currentDate->format('Ym01'));
        $this->lastDay = new \DateTime($currentDate->format('Ymd'));

        $this->oneMonthInterval = new \DateInterval('P1M');
        $this->oneDayInterval = new \DateInterval('P1D');
        $this->periodInterval = new \DateInterval('P3M');

        $isEmpty = empty($currentMonth);
        $baseDate = $currentDate;
        if (!$isEmpty) {
            $baseDate = new \DateTime($currentMonth);
        }

        $this->nextMonth = $baseDate->format('Ym01');
        $this->currentMonth = $this->nextMonth;

        $previous = new \DateTime($this->nextMonth);
        $previous->sub($this->periodInterval);

        $this->previousMonth = $previous->format('Ym01');
    }

    public function getDayMenu(array $days):string
    {

        $dayMenu = '';

        $isContain = count($days);
        if ($isContain) {
            $dayMenu = '<table><tr>';

            foreach ($days as $day) {
                $dayMenu .= "<td>$day</td>";
            }

            $dayMenu .= '</tr></table>';
        }

        return $dayMenu;
    }

    public function getMonthMenu(array $months):string
    {

        $monthMenu = '';

        $isContain = count($months);
        if ($isContain) {
            $monthMenu = '<table><tr>';

            foreach ($months as $month) {
                $monthMenu .= "<td>$month</td>";
            }

            $monthMenu .= '</tr></table>';
        }

        return $monthMenu;
    }

    public function getPagers():array
    {
        $result = array(self::NEXT_MONTH => $this->nextMonth,
            self::PREVIOUS_MONTH => $this->previousMonth,
            self::CURRENT_DAY => $this->currentMonth,);

        return $result;
    }

    public function getDays()
    {
        $day = new \DateTime($this->currentMonth);

        $isValidDay = $this->isValidDay($day);
        if ($isValidDay) {
            $this->currentMonth = $day->format('Ym01');
        }
        if (!$isValidDay) {
            $day = $this->lastDay;
            $this->currentMonth = $day->format('Ym01');
        }

        $daysBegins = new \DateTime($day->format('Ym01'));
        $daysEnds = new \DateTime($daysBegins->format('Ymt'));

        $isLastMonth = $daysBegins <= $this->lastDay && $this->lastDay < $daysEnds;
        $simpleTextBegins = null;
        $simpleTextEnds = null;
        if ($isLastMonth) {
            $simpleTextEnds = $daysEnds;
            $daysEnds = new \DateTime($this->lastDay->format('Ymd'));
            $simpleTextBegins = new \DateTime($this->lastDay->format('Ymd'));
            $simpleTextBegins->add($this->oneDayInterval);
        }

        $daysEnds->add($this->oneDayInterval);
        $dayPeriod = new \DatePeriod($daysBegins, $this->oneDayInterval, $daysEnds);

        $days = array();
        foreach ($dayPeriod as $periodDay) {

            $dayNumber = $periodDay->format('d');
            $dayLink = "<a href='#'>$dayNumber</a>";
            $days[] = $dayLink;
        }

        $letSimpleText = !empty($simpleTextBegins) && !empty($simpleTextEnds);
        if ($letSimpleText) {

            $simpleTextEnds->add($this->oneDayInterval);
            $textPeriod = new \DatePeriod($simpleTextBegins, $this->oneDayInterval, $simpleTextEnds);;

            foreach ($textPeriod as $dayForText) {
                $days[] = $dayForText->format('d');
            }
        }

        return $days;
    }

    public function setCurrentMonth(string $previous, string $next):array
    {

        $startMonth = new \DateTime($previous);
        $finishMonth = new \DateTime($next);

        $months = $this->applyScope($startMonth, $finishMonth);

        return $months;
    }

    public function getNextMonth(string $dayString = ''):array
    {

        $day = $this->setBaseDay($dayString);

        $finishMonth = $day->add($this->oneMonthInterval);

        $months = $this->getNextScope($finishMonth);

        return $months;
    }

    public function getPreviousMonth(string $dayString = self::CALENDAR_BEGINS):array
    {
        $day = $this->setBaseDay($dayString);

        $startMonth = $day->sub($this->oneMonthInterval);

        $months = $this->getPreviousScope($startMonth);

        return $months;
    }

    private function isValidDay(\DateTime $day):bool
    {
        $result = $day >= $this->begins && $day < $this->ends;

        return $result;
    }

    /**
     * @param $startMonth
     * @param $finishMonth
     */
    private function setNextAndPrevious(\DateTime $startMonth, \DateTime $finishMonth)
    {
        $this->nextMonth = $finishMonth->format('Ymd');
        $this->previousMonth = $startMonth->format('Ymd');
    }

    /**
     * @param $startMonth
     * @param $finishMonth
     * @return array
     */
    private function getMonthElements(\DateTime $startMonth, \DateTime $finishMonth):array
    {
        $monthPeriod = new \DatePeriod($startMonth, $this->oneMonthInterval, $finishMonth);

        $months = array();
        foreach ($monthPeriod as $month) {

            $monthNumber = $month->format('Ym01');
            $monthText = $month->format('Y F');

            $isCurrent = $monthNumber == $this->currentMonth;
            $monthElement = "<a class ='month' href='#' data-day='$monthNumber'>$monthText</a>";
            if ($isCurrent) {
                $monthElement = $monthText;
            }

            $months[] = $monthElement;
        }
        return $months;
    }

    /**
     * @param \DateTime $finishMonth
     * @return array
     */
    private function getNextScope(\DateTime $finishMonth):array
    {
        $isValidDay = $this->isValidDay($finishMonth);
        if (!$isValidDay) {
            $finishMonth = $this->ends;
        }

        $startMonth = new \DateTime($finishMonth->format('Ymd'));
        $startMonth->sub($this->periodInterval);

        $isValidDay = $this->isValidDay($startMonth);
        if (!$isValidDay) {
            $startMonth = $this->begins;
            $finishMonth = new \DateTime($startMonth->format('Ymd'));
            $finishMonth->add($this->periodInterval);
        }

        $months = $this->applyScope($startMonth, $finishMonth);

        return $months;
    }

    /**
     * @param string $dayString
     * @return \DateTime
     */
    private function setBaseDay(string $dayString):\DateTime
    {
        $isEmpty = empty($dayString);
        if ($isEmpty) {
            $dayString = $this->ends->format('Ymd');
        }
        $day = new \DateTime($dayString);

        return $day;
    }

    /**
     * @param \DateTime $startMonth
     * @return array
     */
    private function getPreviousScope(\DateTime $startMonth):array
    {
        $isValidDay = $this->isValidDay($startMonth);
        if (!$isValidDay) {
            $startMonth = $this->begins;
        }

        $finishMonth = new \DateTime($startMonth->format('Ymd'));
        $finishMonth->add($this->periodInterval);

        $isValidDay = $this->isValidDay($finishMonth);
        if (!$isValidDay) {
            $finishMonth = $this->ends;
            $startMonth = new \DateTime($finishMonth->format('Ymd'));
            $startMonth->sub($this->periodInterval);
        }

        $months = $this->applyScope($startMonth, $finishMonth);

        return $months;
    }

    /**
     * @param $startMonth
     * @param $finishMonth
     * @return array
     */
    private function applyScope(\DateTime $startMonth, \DateTime $finishMonth):array
    {
        $this->setNextAndPrevious($startMonth, $finishMonth);

        $finishMonth->add($this->oneDayInterval);
        $months = $this->getMonthElements($startMonth, $finishMonth);
        return $months;
    }

}
