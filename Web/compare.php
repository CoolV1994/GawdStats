<?php
/*======================================================================*\
|| #################################################################### ||
|| # GawdScape Statistics Leaderboard                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2015 GawdScape. All Rights Reserved.                  # ||
|| #################################################################### ||
\*======================================================================*/

// ######################### REQUIRE BACK-END ############################
require_once('classes/Arrays.php');
require_once('classes/SQL.php');
require_once('classes/Phrases.php');

function splitArrayCategory($stat, $sep) {
    $array = Arrays::getArray($stat);
    $phrase = Phrases::getPhrase($stat);
    $inserts = null;
    for($i = 0; $i < count($array); $i++) {
        if ($i > 0) {
            $inserts .= $sep;
        }
        if ($stat == "achievement") {
            $img = "<img src='images/achievements/".$array[$i].".png' class='img'> ";
        }
        else if ($stat == "stat.killEntity" || $stat == "stat.entityKilledBy") {
            $img = "<img src='images/mobs/".$array[$i].".png' class='img'> ";
        }
        else if ($stat != "stat") {
            $img = "<img src='images/items/".$array[$i].".png' class='img'> ";
        }
        $inserts .= $img . $phrase[$i];
    }
    return $inserts;
}

function splitArray($array, $sep, $row) {
    if ($row) {
        unset($array["uuid"]);
        if ($array['exploreAllBiomesProgress']) {
            $array['exploreAllBiomesProgress'] = splitArray(unserialize($array['exploreAllBiomesProgress']), ", ", false);
        }
    }
    $inserts = null;
    foreach($array as $value) {
        if ($inserts != null) {
            $inserts .= $sep;
        }
        $inserts .= $value;
    }
    return $inserts;
}

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

$do = $_GET['do'];
$p1 = $_GET['player1'];
$p2 = $_GET['player2'];

if ($do == null) {
    die("Enter Category.");
}
if ($p1 == null) {
    die("Enter Player 1.");
}

?>
<html>
<head>
    <title><?=Phrases::$categories[$do]?> - GawdScape</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <div class="container">
        <div class="column column-main">
            <span class="title">
                <a href="/"><img src='images/logo.png'></a>
                <b><?=Phrases::$categories[$do]?></b>
            </span><br>
            <?php
            if ($do != "stat") {
                echo "<span><img src='images/total.png' class='img'>Total:</span><hr>";
            }
            ?>
            <span><?=splitArrayCategory($do, "</span><hr><span>")?></span>
        </div>
<?php

$start = microtime();
$sql = new SQL();
$sql->connect();

// UUID Lookup for Player 1
$result = $sql->readStatement("SELECT `uuid` FROM `players` WHERE `username` LIKE '%".$p1."%' LIMIT 1");
$row = mysqli_fetch_assoc($result);
// Player 1 Stats
$result = $sql->readStatement("SELECT * FROM `$do` WHERE `uuid`='".$row["uuid"]."' LIMIT 1");
if ($result != null) {
    while($row = mysqli_fetch_assoc($result)) {
        $resultName = $sql->readStatement("SELECT `username` FROM `players` WHERE `uuid`='".$row["uuid"]."' LIMIT 1");
        $rowName = mysqli_fetch_assoc($resultName);
        echo "
        <div class='column column-1'>
            <span class='title'>
                <img src='https://minotar.net/helm/".$rowName["username"]."/32.png' alt='".$rowName["username"]."'>
                <b>".$rowName["username"]."</b>
            </span><br>";
            echo "<span>".splitArray($row, "</span><hr><span>", true)."</span>";
            echo "
        </div>";
    }
}

if ($p2) {
    // UUID Lookup for Player 2
    $result = $sql->readStatement("SELECT `uuid` FROM `players` WHERE `username` LIKE '%".$p2."%' LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    // Player 2 Stats
    $result = $sql->readStatement("SELECT * FROM `$do` WHERE `uuid`='".$row["uuid"]."' LIMIT 1");
    if ($result != null) {
        while($row = mysqli_fetch_assoc($result)) {
            $resultName = $sql->readStatement("SELECT `username` FROM `players` WHERE `uuid`='".$row["uuid"]."' LIMIT 1");
            $rowName = mysqli_fetch_assoc($resultName);
            echo "
        <div class='column column-2'>
            <span class='title'>
                <img src='https://minotar.net/helm/".$rowName["username"]."/32.png' alt='".$rowName["username"]."'>
                <b>".$rowName["username"]."</b>
            </span><br>";
            echo "<span>".splitArray($row, "</span><hr><span>", true)."</span>";
            echo "
        </div>";
        }
    }
}

$sql->close();

?>
    </div>
    <br><br>
    <?=(microtime() - $start)?>
</body>
</html>