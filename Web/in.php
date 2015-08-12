<?php
/*======================================================================*\
|| #################################################################### ||
|| # GawdScape Statistics Leaderboard                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2015 GawdScape. All Rights Reserved.                  # ||
|| #################################################################### ||
\*======================================================================*/

// ########################### CONFIGURATION #############################
$Allowed_IPs = array("192.168.1.1", "127.0.0.1");
$Requester = "GawdScapeStatSync";
// ######################### END CONFIGURATION ###########################

// ############################# SECURITY ################################
// IP White-list
if(!in_array($_SERVER['REMOTE_ADDR'], $Allowed_IPs) {
	die();
}
// Verify requester
if($_SERVER['HTTP_X_REQUESTED_WITH'] != $Requester) {
    die();
}

// ######################### DEFINE VARIABLES ############################
$uuid = $_POST['uuid'];
$name = $_POST['name'];
$data = $_POST['data'];

// ######################### REQUIRE BACK-END ############################
require_once('classes/SQL.php');

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################
if ($uuid && $name && $data) {
    $start = microtime();
    $stats = json_decode($data, true);
    $sql = new SQL();
    $sql->setUserStats($uuid, $stats);
    $sql->connect();
    $sql->writeStatement($sql->insertUsername($name));
    $sql->writeStatement($sql->prepareInsert("achievement", 2));
    $sql->writeStatement($sql->prepareInsert("stat", 0));
    $sql->writeStatement($sql->prepareInsert("stat.entityKilledBy", 0));
    $sql->writeStatement($sql->prepareInsert("stat.killEntity", 0));
    $sql->writeStatement($sql->prepareInsert("stat.mineBlock", 1));
    $sql->writeStatement($sql->prepareInsert("stat.useItem", 1));
    $sql->writeStatement($sql->prepareInsert("stat.breakItem", 1));
    $sql->writeStatement($sql->prepareInsert("stat.craftItem", 1));
    $sql->close();
    echo microtime() - $start;
}
