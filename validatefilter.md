ok for all exept question array gotta work out how to do well

can encode in js in this format

$queryString = "questions[0][key]=hello&questions[0][value]=there&questions[1][key]=hi&questions[1][value]=mom";
parse_str($queryString, $result);

var_dump($result);

but can I filter array of objects

https://stackoverflow.com/questions/4829355/filter-var-array-multidimensional-array

<?php

$input = array(
    // "name" => "zak",
    // "likes" => array(
    //     2, 7, 6
    // ),
    "questions" => array(
        array(
            "key" => "Hello",
            "value" => "There"
        ),
        array(
            "key" => "General",
            "value" => "kenobi"
        )
    )
);

$filter_settings = array(
    // 'name' => array (
    //     'filter' => FILTER_VALIDATE_REGEXP,
    //     'options' => array(
    //         "regexp" => "/^[\w]{3,16}$/"
    //     ),
    //     'flags' => FILTER_NULL_ON_FAILURE
    // ),
    // 'int' => FILTER_VALIDATE_INT,
    // 'likes' => array(
    //     'filter' => FILTER_VALIDATE_INT,
    //     "flags" => FILTER_REQUIRE_ARRAY
    // ),
    'questions' => array(
        'flags' => FILTER_REQUIRE_ARRAY,
        'key' => array(
            'filter' => FILTER_VALIDATE_INT
        )
    )
);

var_dump(filter_var_array($input, $filter_settings));