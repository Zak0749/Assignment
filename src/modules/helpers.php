<?php

namespace helpers;

function randomise_avatar(): string
{
    $newHex = "";
    foreach (range(0, 10) as $_) {
        $newHex .= dechex(rand(0, 16));
    }
    return $newHex;
}

function calculate_streak($user)
{
    if (isset($user["streak_start"]) and isset($user["streak_last"])) {
        $streak_start    = strtotime($user["streak_start"]);
        $streak_last     =  strtotime($user["streak_last"]);
        $date_difference = $streak_last - $streak_start;

        return floor($date_difference / 86400);
    } else {
        return 0;
    }
}

// Uses the predefined function `array_rand` but adds on top getting the actual elements rather than the index reducing code
function random_from_array(array $array, int $num = 1)
{
    $indexes = array_rand($array, $num);

    if ($num == 1) {
        return $array[$indexes];
    } else {
        return array_map(fn ($index) => $array[$index], $indexes);
    }
}
