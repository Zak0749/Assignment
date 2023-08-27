<?php

namespace database;

use SQLite3;

class DB extends SQLite3
{
    function __construct()
    {
        parent::__construct("../database/db.sqlite");
    }
}