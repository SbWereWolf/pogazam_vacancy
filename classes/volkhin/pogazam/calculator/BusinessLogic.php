<?php

namespace Volkhin\Pogazam\Calculator;


class BusinessLogic
{
    const EXPRESSION_KEY = 'expression';



    protected $input;

    private $notifier = null;
    private $expression = null;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    function Process():string
    {

        $notifier = new Notifier();

        $expression = $this->GetExpression();

        $validator = new Validator($expression);
        $isValid = $validator->Validate();
        $total = 0;
        $calculatorNotifier = null;
        if ($isValid) {

            $expression = $validator->expression;
            $calculator = new Calculator($expression);

            $total = $calculator->Calculate();
            $calculatorNotifier = $calculator->notifier;
        }

        $validatorNotifier = $validator->notifier;
        $isEmpty = empty($validatorNotifier);
        if(!$isEmpty){
            $notifier->Merge($validatorNotifier);
        }

        $isEmpty = empty($calculatorNotifier);
        if(!$isEmpty){
            $notifier->Merge($calculatorNotifier);
        }

        $formatter = new Output($total, $notifier);

        $resultString = $formatter->FormatOutput();

        $resultJson = json_encode(['calculation_result' => $resultString]);

        return $resultJson;
    }

    /**
     * @return string
     */
    private function GetExpression():string
    {
        $input = $this->input;

        $isArray = is_array($input);
        $isExists = false;
        if ($isArray) {
            $isExists = array_key_exists(self::EXPRESSION_KEY, $input);
        }

        $expression = '';
        if ($isExists) {
            $expression = $input[self::EXPRESSION_KEY];
            return $expression;
        }
        return $expression;
    }
}
