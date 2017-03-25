<?php

namespace Volkhin\Pogazam;


class Calculator
{
    public $notifier = null;
    public $expression = null;

    public function __construct(Expression $expression)
    {

        $this->notifier = new Notifier();
        $this->expression = $expression;
    }

    public function Calculate():float
    {
        $result = eval('return '.$this->expression->argumentA.$this->expression->operation.$this->expression->argumentB.';');
        return $result;
    }

}
