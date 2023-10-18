<?php

namespace helpers;

function randomise_avatar(): string
{
    // Generate an number between 0 and 4294967295 (00000000 and ffffffff) then puts it into a hex string
    return dechex(rand(0, 4294967295));
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

function get_body(): array
{
    // Get the body text from input file
    $body = file_get_contents("php://input");

    // Decode the file into an associative array and return it
    return json_decode($body, true);
}

// function validate_text(
//     array $object,
//     string $key,
//     bool $required = false,
//     int | null $max_length = null,
//     int | null $min_length = null,
//     string | null $pattern = null
// ): bool {
//     return (!$required || isset($object[$key]))
//         && (!isset($max_length) || strlen($object[$key]) <= $max_length)
//         && (!isset($min_length) || strlen($object[$key]) >= $min_length
//             && (!isset($pattern) || preg_match($object[$key], $pattern))
//         );
// }

function validate_number(
    array $object,
    string $key,
    int | null $max = null,
    int | null $min = null,
): bool {
    return isset($object[$key])
        && is_numeric($object[$key])
        && (!isset($max_length) || strlen(($object[$key])) <= $max)
        && (!isset($min_length) || strlen(($object[$key])) >= $min);
}

function validate_string(array $object, string $key, bool $required, string $pattern): bool
{
    return (!$required || isset($object[$key])) && is_string($object[$key]) && preg_match($pattern, $object[$key]);
}

//  this is silly have different function for array exist, valid and array key validation
function validate_array_key(
    array $object,
    string $array_key,
    string $item_key,
    bool $array_required,
    bool $item_required,
    string $pattern
): bool {
    if (!isset($object[$array_key])) {
        return !$array_required;
    }

    foreach ($object[$array_key] as $item) {
        if (!((!$item_required || isset($item[$item_key])) && preg_match($pattern, $item[$item_key]))) {
            return false;
        }
    }
    return true;
}

function is_array_valid(
    array $object,
    string $key
) {
    return isset($object[$key]) && is_array($object[$key]);
}
