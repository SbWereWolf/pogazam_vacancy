<?php
/**
 * Created by PhpStorm.
 * User: ktokt
 * Date: 25.03.2017
 * Time: 19:46
 */

namespace Volkhin\Pogazam;


class Notifier
{

    protected $warnings = [];
    protected $errors = [];

    public function GetWarnings():array
    {

        return $this->warnings;
    }

    public function GetErrors():array
    {

        return $this->errors;
    }

    public function AddWarning(string $warning):bool
    {

        $this->warnings[] = $warning;

        return true;
    }

    public function AddError(string $error):bool
    {

        $this->errors[] = $error;

        return true;
    }

    public function Merge(Notifier $other):bool
    {

        $isExists = !empty($other);
        $result = false;
        if ($isExists) {
            $otherWarnings = $other->GetWarnings();
            $otherErrors = $other->GetErrors();

            $this->warnings = array_merge($this->warnings,$otherWarnings);
            $this->errors = array_merge($this->errors,$otherErrors);

            $result = true;
        }

        return $result;
    }


}
