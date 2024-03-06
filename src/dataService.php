<?php

class DataService {
    private $db;

    public function __construct(mysqli $database) {
        $this->db = $database;
    }

    public static function getDatabaseConnection() {
        return new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);
    }

    public function initGame() {
        $this->db->prepare('INSERT INTO games VALUES ()')->execute();

        return $this->db->insert_id;
    }

    public function registerMove($gameId, $type, $moveFrom, $moveTo, $lastMove, $state) {
        $stmt = $this->db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssis', $gameId, $type, $moveFrom, $moveTo, $lastMove, $state);
        $stmt->execute();

        return $this->db->insert_id;
    }

    public function getMove($moveId) {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ?');
        $stmt->bind_param('i', $moveId);
        $stmt->execute();

        return $stmt->get_result()->fetch_array();
    }

    public function getPreviousGameMoves($gameId) {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE game_id = ?');
        $stmt->bind_param('i', $gameId);    // Make sure we bind the parameter to the statement. Otherwise injection is possible.
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }
}