<?php
/* @var $months array */
/* @var $days array */
/* @var $pager array */

use \Volkhin\Pogazam\Calendar\Calendar;

?>
<html>
<head>
    <style>
        table {
            width: 100%;
        }

        td {
            border: 1px solid #666;
        }

        div {
            max-width: 750px;
        }

        .calendar {
            background-color: #EEE;
        }

        body {
            width: 750px;
            margin: auto;
            max-width: 100%;
        }

    </style>
</head>
<body>
<h1>Calendar</h1>
<div id="calendar" class="calendar">
    <?php
    $isSet = isset($pager);
    $isArray = false;
    if ($isSet) {
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
        <table>
            <tr>
                <td><a id="previous-link" href="#" onclick="movePrevious();">PREVIOUS</a></td>
                <?php
                $isSet = isset($months);
                $isArray = false;
                $isContain = false;
                if ($isSet) {
                    $isArray = is_array($months);
                    $isContain = count($months) > 0;
                } ?>
                <td id="months-menu">
                    <?php
                    if ($isArray && $isContain) :?>

                        <table>
                            <tr>
                                <?php foreach ($months as $month): ?>
                                    <td><?= $month ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </table>

                    <?php endif ?>
                </td>
                <td><a id="next-link" href="#" onclick="moveNext();">NEXT</a></td>
            </tr>
        </table>
    </div>
    <?php
    $isSet = isset($pager);
    $isArray = false;
    if ($isSet) {
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
    <div id="days-menu">
        <?php
        $isSet = isset($days);
        $isArray = false;
        $isContain = false;
        if ($isSet) {
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
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<script>

    $(document).ready(function () {

        $('.month').live('click', function () {

            var month_pager = $("#months-pager");

            const previous = month_pager.data("previous");
            const next = month_pager.data("next");
            const day = $(this).data("day");

            $.ajax({
                type: 'POST',
                url: '/pogazam/calendar/current/' + day,
                data: {previous: previous, next: next},
                dataType: 'json',
                success: function (result) {
                    setMonthMenu(result);
                    setDayMenu(result);
                }
            });

        });

    });

    function movePrevious() {

        const previous = $("#months-pager").data("previous");
        const current = $("#days-pager").data("current");

        $.ajax({
            type: 'POST',
            url: '/pogazam/calendar/previous/' + previous,
            data: {current: current},
            dataType: 'json',
            success: function (result) {

                setMonthMenu(result);
            }
        });
    }
    function moveNext() {

        const next = $("#months-pager").data("next");
        const current = $("#days-pager").data("current");

        $.ajax({
            type: 'POST',
            url: '/pogazam/calendar/next/' + next,
            data: {current: current},
            dataType: 'json',
            success: function (result) {

                setMonthMenu(result);
            }
        });
    }

    function setMonthMenu(result) {

        var months_pager = $('#months-pager');

        const months_pager_previous = result.previous;
        months_pager.data("previous", months_pager_previous);

        const months_pager_next = result.next;
        months_pager.data("next", months_pager_next);

        const months_menu_html = result.months_menu;
        $("#months-menu").html(months_menu_html);
    }

    function setDayMenu(result) {

        var days_pager = $('#days-pager');

        const days_pager_current = result.current;
        days_pager.data("current", days_pager_current);

        const days_menu_html = result.days_menu;
        $("#days-menu").html(days_menu_html);
    }
</script>
</html>
