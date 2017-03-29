<?php

namespace Volkhin\Pogazam\Calendar;

class Calendar
{
    const CALENDAR_BEGINS = '20150101';
    const MONTH_PERIOD_COUNT = 4;

    const NEXT_MONTH = 'next';
    const PREVIOUS_MONTH = 'previous';
    const CURRENT_DAY = 'current';

    private $ends;
    private $begins;
    private $lastDay;

    private $oneMonthInterval;
    private $oneDayInterval;
    private $periodInterval;

    private $currentDay ='';
    private $nextMonth ='';
    private $previousMonth = '';

    public function __construct()
    {
        $currentDate = new \DateTime();

        $this->begins = new \DateTime(self::CALENDAR_BEGINS);
        $this->ends = new \DateTime($currentDate->format('Ym01'));
        $this->lastDay = new \DateTime($currentDate->format('Ymd'));

        $this->oneMonthInterval = new \DateInterval('P1M');
        $this->oneDayInterval = new \DateInterval('P1D');
        $this->periodInterval = new \DateInterval('P3M');
    }

    public function getPagers():array{
        $result = array(self::NEXT_MONTH=>$this->nextMonth,
            self::PREVIOUS_MONTH=>$this->previousMonth,
            self::CURRENT_DAY=>$this->currentDay,);

        return $result;
    }

    public function getDays(string $dayString = null){

        $isEmpty = empty($dayString);
        if($isEmpty){
            $dayString=$this->lastDay->format('Ymd');
        }
        $day = new \DateTime($dayString);

        $isValidDay = $this->isValidDay($day);
        if($isValidDay){
            $this->currentDay = $day->format('Ymd');
        }
        if (!$isValidDay) {
            $day = $this->lastDay;
            $this->currentDay = $day->format('Ymd');
        }

        $daysBegins = new \DateTime($day->format('Ym01'));
        $daysEnds = new \DateTime($daysBegins->format('Ymt'));
        $daysEnds->add($this->oneDayInterval);
        $dayPeriod = new \DatePeriod($daysBegins, $this->oneDayInterval, $daysEnds);

        $days = array();
        foreach ($dayPeriod as $periodDay) {
            $days[] = $periodDay->format('d');
        }

        return $days;
    }

    public function getNextMonth(string $dayString = null):array
    {

        $isEmpty = empty($dayString);
        if($isEmpty){
            $dayString=$this->ends->format('Ymd');
        }
        $day = new \DateTime($dayString);

        $finishMonth = $day->add($this->oneMonthInterval);

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

        $finishMonth->add($this->oneDayInterval);
        $monthPeriod = new \DatePeriod($startMonth, $this->oneMonthInterval, $finishMonth);

        $months = array();
        foreach ($monthPeriod as $month) {
            $months[] = $month->format('F Y');
        }

        $finishMonth->sub($this->oneDayInterval);
        $this->setNextAndPrevious($finishMonth, $startMonth);

        /*
        $isMore = count($months) > self::MONTH_PERIOD_COUNT;
        if ($isMore) {
            unset($months[0]);
            $this->previousMonth = $months[1];
        }
        */

        return $months;
    }

    public function getPreviousMonth(string $dayString = self::CALENDAR_BEGINS):array
    {
        $day = new \DateTime($dayString);

        $startMonth = $day->sub($this->oneMonthInterval);

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

        $finishMonth->add($this->oneDayInterval);
        $monthPeriod = new \DatePeriod($startMonth, $this->oneMonthInterval, $finishMonth);

        $months = array();
        foreach ($monthPeriod as $month) {
            $months[] = $month->format('F Y');
        }

        $finishMonth->sub($this->oneDayInterval);
        $this->setNextAndPrevious($finishMonth, $startMonth);
/*
        $isMore = count($months) > self::MONTH_PERIOD_COUNT;
        if ($isMore) {
            unset($months[0]);
        }
        */

        return $months;
    }

    private function isValidDay(\DateTime $day):bool
    {
        $result = $day >= $this->begins && $day < $this->ends;

        return $result;
    }

    /**
     * @param $finishMonth
     * @param $startMonth
     */
    private function setNextAndPrevious($finishMonth, $startMonth)
    {
        $this->nextMonth = $finishMonth->format('Ymd');
        $this->previousMonth = $startMonth->format('Ymd');
    }

}
