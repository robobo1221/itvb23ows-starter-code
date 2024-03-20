<?php

class AiService {
    public function getMove($board, $hand, $lastMoveNum) {
        $connection = curl_init('hive-ai:5000');

        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode([
            "board" => $board,
            "hand" => $hand,
            "move_number" => $lastMoveNum
        ]));
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($connection, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($connection);
        curl_close($connection);

        return json_decode($result, true);
    }
}