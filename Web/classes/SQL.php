<?php
/*======================================================================*\
|| #################################################################### ||
|| # GawdScape Statistics Leaderboard                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2015 GawdScape. All Rights Reserved.                  # ||
|| #################################################################### ||
\*======================================================================*/

// ######################### REQUIRE BACK-END ############################
require_once('Arrays.php');

class SQL {

// ########################### CONFIGURATION #############################
    private $dbHost = "localhost";
    private $dbUser = "root";
    private $dbPass = "password";
    private $dbName = "gawdstats";
// ######################### END CONFIGURATION ###########################

// ######################### DEFINE VARIABLES ############################
    private $conn;
    private $uuid;
    private $stats;

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

    function __construct() {}

    public function setUserStats($uuid, $stats) {
        $this->uuid = $uuid;
        $this->stats = $stats;
    }

    public function insertUsername($username) {
        $statement = "
INSERT INTO  `".$this->dbName."`.`players` (`uuid`, `username`)
VALUES ('".$this->uuid."', '$username')
ON DUPLICATE KEY UPDATE `username`='$username'
;";
        return $statement;
    }

    private function getInsertArrayAchievement() {
        $inserts = null;
        $values = null;
        $updates = null;
        $total = 0;
        foreach(Arrays::getArray("achievement") as $value) {
            if ($inserts != null) {
                $inserts .= ", ";
                $values .= ", ";
                $updates .= ", ";
            }
            if ($value == "exploreAllBiomes") {
                if ($this->stats['achievement.exploreAllBiomes']['value'] > 0) {
                    $total++;
                }
                $inserts .= "`$value`";
                $values .= "'".$this->stats['achievement.exploreAllBiomes']['value']."'";
                $updates .= "`$value`='".$this->stats['achievement.exploreAllBiomes']['value']."'";
                continue;
            }
            if ($value == "exploreAllBiomesProgress") {
                $inserts .= "`$value`";
                $serial = serialize($this->stats['achievement.exploreAllBiomes']['progress']);
                $values .= "'$serial'";
                $updates .= "`$value`='$serial'";
                continue;
            }
            if ($this->stats['achievement.'.$value] > 0) {
                $total++;
            }
            $inserts .= "`$value`";
            $values .= "'".$this->stats['achievement.'.$value]."'";
            $updates .= "`$value`='".$this->stats['achievement.'.$value]."'";
        }
        $inserts = "`total`, " . $inserts;
        $values = "'$total', " . $values;
        $updates = "`total`='$total', " . $updates;
        return array($inserts, $values, $updates);
    }

    private function getInsertArray($stat) {
        $inserts = null;
        $values = null;
        $updates = null;
        $total = 0;
        foreach(Arrays::getArray($stat) as $value) {
            if ($inserts != null) {
                $inserts .= ", ";
                $values .= ", ";
                $updates .= ", ";
            }
            $inserts .= "`$value`";
            $values .= "'".$this->stats[$stat.'.'.$value]."'";
            $updates .= "`$value`='".$this->stats[$stat.'.'.$value]."'";
            $total += $this->stats[$stat.'.'.$value];
        }
        if ($stat != "stat") {
            $inserts = "`total`, " . $inserts;
            $values = "'$total', " . $values;
            $updates = "`total`='$total', " . $updates;
        }
        return array($inserts, $values, $updates);
    }

    private function getInsertArrayItem($stat) {
        $inserts = null;
        $values = null;
        $updates = null;
        $total = 0;
        foreach(Arrays::getArray($stat) as $value) {
            if ($inserts != null) {
                $inserts .= ", ";
                $values .= ", ";
                $updates .= ", ";
            }
            $inserts .= "`$value`";
            $values .= "'".$this->stats[$stat.'.minecraft.'.$value]."'";
            $updates .= "`$value`='".$this->stats[$stat.'.minecraft.'.$value]."'";
            $total += $this->stats[$stat.'.minecraft.'.$value];
        }
        $inserts = "`total`, " . $inserts;
        $values = "'$total', " . $values;
        $updates = "`total`='$total', " . $updates;
        return array($inserts, $values, $updates);
    }

    public function prepareInsert($stat, $mode) {
        switch ($mode) {
            case 1:
                $data = $this->getInsertArrayItem($stat);
                break;
            case 2:
                $data = $this->getInsertArrayAchievement();
                break;
            default:
                $data = $this->getInsertArray($stat);
                break;
        }
        $statement = "
INSERT INTO `".$this->dbName."`.`$stat` (`uuid`, ".$data[0].")
VALUES ('".$this->uuid."', ".$data[1].")
ON DUPLICATE KEY UPDATE ".$data[2]."
;";
        return $statement;
    }

    public function connect() {
        // Create connection
        $this->conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        // Check connection
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function close() {
        mysqli_close($this->conn);
    }

    public function writeStatement($sql) {
        if (mysqli_query($this->conn, $sql)) {
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($this->conn) . "<br>\r\n";
            return false;
        }
    }

    public function readStatement($sql) {
        $result = mysqli_query($this->conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            return $result;
        } else {
            return null;
        }
    }

}
