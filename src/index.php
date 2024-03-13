<?php
    include_once "util.php";
    include_once "dataService.php";
    include_once "rules.php";
    include_once "hive.php";

    $databaseConnection = DataService::getDatabaseConnection();
    $dataService = new DataService($databaseConnection);
    $hive = new Hive($dataService);

    $to = $hive->getPossibleMoves();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['piece']) && isset($_POST['to']) && isset($_POST['Play'])) {
            $hive->play($_POST['piece'], $_POST['to']);
        } else if (isset($_POST['from']) && isset($_POST['to']) && isset($_POST['Move'])) {
            $hive->move($_POST['from'], $_POST['to']);
        } else if (isset($_POST['Pass'])) {
            $hive->pass();
        } else if (isset($_POST['Undo'])) {
            $hive->undo();
        } else if (isset($_POST['Restart'])) {
            $hive->restart();
        }
        header('Location: index.php');
    }
?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="board">
            <?php
                $hive->renderBoard();
            ?>
        </div>
        <div class="hand">
            White:
            <?php
                foreach ($hive->getHand(0) as $tile => $ct) {
                    for ($i = 0; $i < $ct; $i++) {
                        echo '<div class="tile player0"><span>'.$tile."</span></div> ";
                    }
                }
            ?>
        </div>
        <div class="hand">
            Black:
            <?php
            foreach ($hive->getHand(1) as $tile => $ct) {
                for ($i = 0; $i < $ct; $i++) {
                    echo '<div class="tile player1"><span>'.$tile."</span></div> ";
                }
            }
            ?>
        </div>
        <div class="turn">
            Turn: <?php if ($hive->getPlayer() == 0) echo "White"; else echo "Black"; ?>
            <?php $hive->printWinner(); ?>
        </div>
        <form method="post" action="index.php">
            <select name="piece">
                <?php
                    $availableTiles = $hive->getAvailableTiles();
                    foreach ($availableTiles as $tile) {
                        echo "<option value=\"$tile\">$tile</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($to as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" name="Play" value="Play">
        </form>

        <form method="post" action="index.php">
            <select name="from">
                <?php
                    $from = $hive->getPossibleFromMoves();

                    foreach ($from as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($hive->getAllMoves() as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" name="Move" value="Move">
        </form>
        <form method="post" action="index.php">
            <input type="submit" name="Pass" value="Pass">
        </form>
        <form method="post" action="index.php">
            <input type="submit" name="Restart" value="Restart">
        </form>
        <strong><?php 
        if (isset($_SESSION['error'])) {
            echo($_SESSION['error']);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            unset($_SESSION['error']);
        } ?></strong>
        <ol>
            <?php
                $moves = $dataService->getPreviousGameMoves($_SESSION['game_id']);
                while ($row = $moves->fetch_array()) {
                    echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
                }
            ?>
        </ol>
        <form method="post" action="index.php">
            <input type="submit" name="Undo" value="Undo">
        </form>
    </body>
</html>
