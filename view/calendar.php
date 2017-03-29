<?php
/* @var $months array */
/* @var $days array */
/* @var $pager array */

use \Volkhin\Pogazam\Calendar\Calendar;

?>

<h1>Calendar</h1>
<div id="calendar">
    <?php
    $isSet = isset($pager);
    $isArray = false;
    if($isSet){
        $isArray = is_array($pager);
    }

    $isNextExists = false;
    $isPreviousExists = false;
    if ($isArray) {
        $isNextExists = array_key_exists(Calendar::NEXT_MONTH, $pager);
        $isPreviousExists = array_key_exists(Calendar::PREVIOUS_MONTH, $pager);
    }

    $next = Calendar::CALENDAR_BEGINS;
    if ($isNextExists) {
        $next = $pager[Calendar::NEXT_MONTH];
    }

    $previous = Calendar::CALENDAR_BEGINS;
    if ($isPreviousExists) {
        $previous = $pager[Calendar::PREVIOUS_MONTH];
    }
    ?>
    <div id="months-pager" data-next="<?= $next ?>" data-previous="<?= $previous ?>"></div>
    <div id="months">
        <?php
        $isSet = isset($months);
        $isArray = false;
        $isContain= false;
        if($isSet){
            $isArray = is_array($months);
            $isContain = count($months) > 0;
        }

        if ($isArray && $isContain) :?>
            <table>
                <tr>
                    <?php foreach ($months as $month): ?>
                        <td>
                            <?= $month ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        <?php endif ?>
    </div>
    <?php
    $isSet = isset($pager);
    $isArray = false;
    if($isSet){
        $isArray = is_array($pager);
    }

    $isCurrentExists = false;
    if ($isArray) {
        $isCurrentExists = array_key_exists(Calendar::CURRENT_DAY, $pager);
    }

    $current = Calendar::CALENDAR_BEGINS;
    if ($isCurrentExists) {
        $current = $pager[Calendar::CURRENT_DAY];
    }
    ?>
    <div id="days-pager" data-current="<?= $current ?>"></div>
    <div id="days">
        <?php
        $isSet = isset($days);
        $isArray = false;
        $isContain= false;
        if($isSet){
            $isArray = is_array($days);
            $isContain = count($days) > 0;
        }
        if ($isArray && $isContain) :?>
            <table>
                <tr>
                    <?php foreach ($days as $day): ?>
                        <td>
                            <?= $day ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        <?php endif ?>
    </div>
</div>
