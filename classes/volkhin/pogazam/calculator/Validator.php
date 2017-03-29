<?php

namespace Volkhin\Pogazam\Calculator;


class Validator
{
    const ARGUMENTS_NUMBER = 2;
    const ARGUMENT_A_INDEX = 0;
    const ARGUMENT_B_INDEX = 1;

    const OPERATIONS_LIMIT = 1;

    const OPERATION = 0;
    const POSITION = 1;

    protected $isAdd = false;
    protected $isDivide = false;
    protected $isMultiply = false;
    protected $isSubtract = false;

    public $notifier = null;
    public $expression = null;
    protected $rawExpression = '';

    public function __construct($expression)
    {

        $this->notifier = new Notifier();
        $this->expression = new Expression();
        $this->rawExpression = $expression;
    }

    public function Validate():bool
    {

        $isEmptyExpression = $this->isEmptyExpression();
        $this->setOperationsPositions($isEmptyExpression);
        $operationsCount = $this->countOperations();
        $isExistsOperation = $this->isOperationExists($operationsCount);
        $operation = $this->getOperation($isExistsOperation);
        $this->isManyOperations($operationsCount, $operation);
        $expressionParts = $this->getExpressionParts($operation);
        $isManyArguments = $this->isManyArguments($expressionParts);
        $this->getRawArguments($expressionParts, $argumentA, $rawArgumentB);
        $isAFloat = $this->setArgumentA($argumentA);
        $argumentB = $this->cleanUpArgument($operation, $rawArgumentB);
        $isBFloat = $this->setArgumentB($argumentB, $rawArgumentB);

        $isError = $isEmptyExpression || (!$isExistsOperation) || (!$isAFloat) || (!$isBFloat);
        $result = !$isError;

        if ($isManyArguments && !$isError) {
            $this->notifier->AddWarning(
                "Первый аргумент : "
                .$this->expression->argumentA
                ."; Второй аргумент : "
                .$this->expression->argumentB);
        }

        return $result;
    }

    /**
     * @return int
     */
    private function countOperations():int
    {
        $operationsCount = 0;
        if ($this->isAdd) {
            $operationsCount++;
        }
        if ($this->isDivide) {
            $operationsCount++;
        }
        if ($this->isMultiply) {
            $operationsCount++;
        }
        if ($this->isSubtract) {
            $operationsCount++;
        }
        return $operationsCount;
    }

    /**
     * @param $operationsCount
     * @return bool
     */
    private function isOperationExists($operationsCount):bool
    {
        $isExistsOperation = $operationsCount > 0;
        if (!$isExistsOperation) {
            $this->notifier->AddError('Не удалось определить арифметическую операцию;');
            return $isExistsOperation;
        }
        return $isExistsOperation;
    }

    /**
     * @param bool $isExistsOperation
     * @return string
     */
    private function getOperation(bool $isExistsOperation):string
    {
        $operation = Expression::OPERATION_UNDEFINED;

        if ($isExistsOperation) {
            $addOperation[self::OPERATION] = Expression::OPERATION_ADD;
            $addOperation[self::POSITION] = $this->isAdd;
            $divideOperation[self::OPERATION] = Expression::OPERATION_DIVIDE;
            $divideOperation[self::POSITION] = $this->isDivide;
            $multiplyOperation[self::OPERATION] = Expression::OPERATION_MULTIPLY;
            $multiplyOperation[self::POSITION] = $this->isMultiply;
            $subtractOperation[self::OPERATION] = Expression::OPERATION_SUBTRACT;
            $subtractOperation[self::POSITION] = $this->isSubtract;

            $operations = array($addOperation, $divideOperation, $multiplyOperation, $subtractOperation);
            $operationPosition = 0;
            foreach ($operations as $candidate) {

                $isPositionDefined = $candidate[self::POSITION] > 0;
                $isLess = $candidate[self::POSITION] < $operationPosition || $operationPosition == 0;
                if ($isPositionDefined && $isLess) {

                    $operationPosition = $candidate[self::POSITION];
                    $operation = $candidate[self::OPERATION];
                }
            }
            $this->expression->operation = $operation;
        }
        return $operation;
    }

    /**
     * @param $operationsCount
     * @param $operation
     * @return bool
     */
    private function isManyOperations($operationsCount, $operation):bool
    {
        $isManyOperations = $operationsCount > self::OPERATIONS_LIMIT;
        if ($isManyOperations) {
            $this->notifier->AddWarning("Операций больше одной, будет выплнена только первая('$operation');");
        }

        return $isManyOperations;
    }

    /**
     * @param $operation
     * @return array
     */
    private function getExpressionParts($operation):array
    {
        $isDefined = $operation != Expression::OPERATION_UNDEFINED;
        $rawExpressionParts = array();
        if ($isDefined) {
            $rawExpressionParts = explode($operation, $this->rawExpression);
        }

        $expressionParts = array();
        if (count($rawExpressionParts) > 0) {
            foreach ($rawExpressionParts as $part) {

                $isEmptyPart = empty($part);
                if (!$isEmptyPart) {
                    $expressionParts[] = $part;
                }
            }
        }
        return $expressionParts;
    }

    /**
     * @param $expressionParts
     * @return bool
     */
    private function isManyArguments($expressionParts):bool
    {
        $partsNumber = count($expressionParts);
        $isManyArguments = $partsNumber > self::ARGUMENTS_NUMBER;
        if ($isManyArguments) {
            $this->notifier->AddWarning('Обнаружено больше двух аргументов'
                . ', для рассчёта будут использованы только первые два;');
        }
        return $isManyArguments;
    }

    /**
     * @param $expressionParts
     * @param $argumentA
     * @param $rawArgumentB
     */
    private function getRawArguments(&$expressionParts, &$argumentA, &$rawArgumentB)
    {
        $partsNumber = count($expressionParts);
        $isEnough = $partsNumber >= self::ARGUMENTS_NUMBER;
        $argumentA = '';
        $rawArgumentB = '';
        if ($isEnough) {
            $argumentA = $expressionParts[self::ARGUMENT_A_INDEX];
            $rawArgumentB = $expressionParts[self::ARGUMENT_B_INDEX];
        }
    }


    /**
     * @param $argumentA
     * @return bool
     */
    private function setArgumentA($argumentA):bool
    {
        $isArgumentADefined = !empty($argumentA);
        $floatA = 0;
        $floatAPosition = false;
        if ($isArgumentADefined) {

            $floatA = floatval($argumentA);
            $stringA = strval($floatA);
            $floatAPosition = mb_strpos($argumentA, $stringA);
        }

        $isAFloat = $floatAPosition !== false;
        if ($isAFloat) {
            $this->expression->argumentA = $floatA;
        }
        if (!$isAFloat) {
            $this->notifier->AddError("Не удалось определить первый аргумент($argumentA);");
        }

        return $isAFloat;
    }

    /**
     * @param $operation
     * @param $rawArgumentB
     * @return string
     */
    private function cleanUpArgument(string $operation, string $rawArgumentB):string
    {
        $isEmpty = empty($operation);
        $exclude = '';
        if (!$isEmpty) {
            $exclude = str_replace($operation, '', Expression::OPERATIONS);
        }

        $argumentB = '';
        $letExclude = !empty($exclude);
        if ($letExclude) {
            $exclude = str_replace($operation, '', Expression::OPERATIONS);
            $excludeBy = str_split($exclude);
            $workpiece = $rawArgumentB;
            foreach ($excludeBy as $pattern) {
                $workpiece = str_replace($pattern, ' ', $workpiece);
            }
            $argumentB = $workpiece;
        }
        return $argumentB;
    }


    /**
     * @param $argumentB
     * @param $rawArgumentB
     * @return bool
     */
    private function setArgumentB($argumentB, $rawArgumentB):bool
    {
        $isArgumentBDefined = !empty($argumentB);
        $floatB = 0;
        $floatBPosition = false;
        if ($isArgumentBDefined) {

            $floatB = floatval($argumentB);
            $stringB = strval($floatB);
            $floatBPosition = mb_strpos($rawArgumentB, $stringB);
        }

        $isBFloat = $floatBPosition !== false;
        if ($isBFloat) {
            $this->expression->argumentB = $floatB;
        }
        if (!$isBFloat) {
            $this->notifier->AddError("Не удалось определить второй аргумент($rawArgumentB);");
        }

        return $isBFloat;
    }

    /**
     * @return bool
     */
    private function isEmptyExpression():bool
    {
        $isEmptyExpression = empty($this->rawExpression);
        if ($isEmptyExpression) {
            $this->notifier->AddError('Выражение - пустое;');
            return $isEmptyExpression;
        }
        return $isEmptyExpression;
    }

    /**
     * @param $isEmptyExpression
     */
    private function setOperationsPositions($isEmptyExpression)
    {
        if (!$isEmptyExpression) {
            $this->isAdd = mb_strpos($this->rawExpression, Expression::OPERATION_ADD);
            $this->isDivide = mb_strpos($this->rawExpression, Expression::OPERATION_DIVIDE);
            $this->isMultiply = mb_strpos($this->rawExpression, Expression::OPERATION_MULTIPLY);
            $this->isSubtract = mb_strpos($this->rawExpression, Expression::OPERATION_SUBTRACT);
        }
    }

}
