<?php
/**
 * Created by PhpStorm.
 * User: ktokt
 * Date: 25.03.2017
 * Time: 19:54
 */

namespace Volkhin\Pogazam\Calculator;


class Expression
{
    const OPERATION_ADD = '+';
    const OPERATION_SUBTRACT = '-';
    const OPERATION_DIVIDE = '/';
    const OPERATION_MULTIPLY = '*';

    const OPERATION_UNDEFINED = '';

    const OPERATIONS = self::OPERATION_ADD . self::OPERATION_SUBTRACT . self::OPERATION_DIVIDE . self::OPERATION_MULTIPLY;

    public $argumentA = 0;
    public $argumentB = 0;
    public $operation = self::OPERATION_UNDEFINED;
}
