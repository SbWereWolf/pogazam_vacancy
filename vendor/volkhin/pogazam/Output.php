<?php

namespace Volkhin\Pogazam;


class Output
{

    const TOTAL_FORMAT = ' Итого : %G';
    const WARNING_FORMAT = 'Предупреждение : %s';
    const ERROR_FORMAT = 'В процессе вычисления произошла ошибка : %s';

    const MESSAGES_SEPARATOR = ' ';

    private $total = 0;
    private $notifier = null;

    public function __construct(float $total, Notifier $notifier)
    {
        $this->total = $total;
        $this->notifier = $notifier;
    }

    public function FormatOutput():string
    {

        $totalFormated = $this->FormatTotal();
        $warningFormated = $this->FormatWarning();
        $errorFormated = $this->FormatError();

        $strings = array($totalFormated, $warningFormated, $errorFormated);
        $output = [];
        foreach ($strings as $string) {

            $isEmpty = empty($string);
            if (!$isEmpty) {
                $output[] = $string;
            }
        }

        $result = implode(self::MESSAGES_SEPARATOR, $output);

        return $result;
    }

    private function FormatTotal():string
    {
        $formated = $this->FormatMessage(self::TOTAL_FORMAT, $this->total);

        return $formated;
    }

    private function FormatWarning():string
    {
        $warnings = $this->notifier->GetWarnings();
        $warning = implode(self::MESSAGES_SEPARATOR, $warnings);
        $formated = $this->FormatMessage(self::WARNING_FORMAT, $warning);

        return $formated;
    }

    private function FormatError():string
    {
        $errors = $this->notifier->GetErrors();
        $error  = implode(self::MESSAGES_SEPARATOR, $errors);
        $formated = $this->FormatMessage(self::ERROR_FORMAT, $error);

        return $formated;
    }

    private function FormatMessage(string $format,$value):string
    {
        $isExists = !empty($value);
        $formatedMessage = '';
        if ($isExists) {
            $formatedMessage = sprintf($format, $value);
        }

        return $formatedMessage;
    }

}
