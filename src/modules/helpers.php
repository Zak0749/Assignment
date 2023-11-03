<?php

namespace helpers;

function randomise_avatar(): string
{

    // Generate an number between 0 and 4294967295 (00000000 and ffffffff) then puts it into a hex string which is padded to ensure 8 characters
    return str_pad(dechex(rand(0, 4294967295)), 8);
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
