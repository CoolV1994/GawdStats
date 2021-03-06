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

function splitArrayCategory($stat, $sep, $page) {
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
        $link = "top.php?do=$stat&page=$page&sort=".$array[$i];
        $asc = " <a href='$link&order=ASC'><img src='images/up.png' class='sort'></a>";
        $desc = " <a href='$link&order=DESC'><img src='images/down.png' class='sort'></a>";
        $inserts .= $img . $phrase[$i] . $asc . $desc;
    }
    return $inserts;
}

function splitArray($array, $sep, $row) {
    if ($row) {
        unset($array["uuid"]);
    }
    if ($array['exploreAllBiomesProgress']) {
        $array['exploreAllBiomesProgress'] = splitArray(unserialize($array['exploreAllBiomesProgress']), ", ", false);
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
$page = $_GET['page'];
$sort = $_GET['sort'];
$order = $_GET['order'];

if ($do == null) {
    die("Enter Category.");
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
                $link = "top.php?do=$stat&page=$page&sort=".$array[$i];
                $asc = "<a href='$link&order=ASC'><img src='images/up.png' class='sort'></a>";
                $desc = "<a href='$link&order=DESC'><img src='images/down.png' class='sort'></a>";
                echo "<span><img src='images/total.png' class='img'> Total: $asc $desc</span><hr>";
            }
            ?>
            <span><?=splitArrayCategory($do, "</span><hr><span>", $page)?></span>
        </div>
<?php

$start = microtime();
$sql = new SQL();
$sql->connect();

if ($sort == null) {
    $sort = "total";
    if ($do == "stat") {
        $sort = "playOneMinute";
    }
}
if (!$order == "ASC") {
    $order = "DESC";
}

$result = $sql->readStatement("SELECT * FROM `$do` ORDER BY `$sort` $order LIMIT ".($page*5).", 5");
if ($result != null) {
    $i = 1;
    while($row = mysqli_fetch_assoc($result)) {
        $resultName = $sql->readStatement("SELECT `username` FROM `players` WHERE `uuid`='".$row["uuid"]."' LIMIT 1");
        $rowName = mysqli_fetch_assoc($resultName);
        echo "
        <div class='column column-$i'>
            <span class='title'>
                <img src='https://minotar.net/helm/".$rowName["username"]."/32.png' alt='".$rowName["username"]."'>
                <b>".$rowName["username"]."</b>
            </span><br>";
        echo "<span>".splitArray($row, "</span><hr><span>", true)."</span>";
        echo "
        </div>";
		$i++;
    }
}

$sql->close();

?>
    </div>
    <br>
    <form name="nav">
        <select name="pageDrop" onChange="document.location.href='top.php?do=<?=$do?>&page='+document.nav.pageDrop.selectedIndex">
        <?php
        for ($i = 0; $i < 10; $i++) {
            $selected = null;
            if ($page == $i) {
                $selected = " selected";
            }
            echo "
            <option$selected>Page ".($i+1)."</option>";
        }
        ?>
        </select>
    </form>
    <br><br>
    <?=(microtime() - $start)?>
</body>
</html>