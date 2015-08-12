<?php
/*======================================================================*\
|| #################################################################### ||
|| # GawdScape Statistics Leaderboard                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2015 GawdScape. All Rights Reserved.                  # ||
|| #################################################################### ||
\*======================================================================*/

// #################### DEFINE IMPORTANT CONSTANTS #######################
$cat = $_GET['cat'];
$img = $_GET['img'];

// ######################### REQUIRE BACK-END ############################
require_once('classes/Arrays.php');
require_once('classes/Images.php');
require_once('classes/Phrases.php');

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

if ($cat && $img) {
    $index = array_search($img, Arrays::getArray($cat));
    header("Location: ".Images::getUrl($cat, $index));
}