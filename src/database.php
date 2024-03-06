<?php
namespace Database;

use mysqli;

class GameState {
    public static function setState($state) {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }
}

class DatabaseConnection {
    private \mysqli $mysqli;

    public function __construct() {
        $this->mysqli = new \mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);
    }

    public function getMysqli(): mysqli {
        return $this->mysqli;
    }
}
