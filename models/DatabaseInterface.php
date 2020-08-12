<?php

namespace Models;

use PDO;

interface DatabaseInterface
{

    public static function get(): PDO;

}