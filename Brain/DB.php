<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 17/04/2017
 * Time: 12:56 PM
 */


/**
 * Class DB - base work with DB
 */

class DB {
    var $dbConnect;
    private $table;

    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    function __construct()
    {
        $file = fopen("/Users/deniz/DB/db.config", "r");
        $str = fgets($file);
        $arr = unserialize($str);

        $this->dbConnect = new mysqli($arr["host"], $arr['username'], $arr["password"], $arr["database"]);

        // Check connection
        if ($this->dbConnect->connect_error) {
            die("Connection failed: " . $this->dbConnect->connect_error);
        }
    }

    /**
     * @param Select select
     * @return array with associations: column => value
     */
    public function selectAAA(Select $select) {

        $select->From($this->getTable());
        $sql = "" . $select;

        $result = $this->dbConnect->query($sql);

        if ($result && $result->num_rows > 0) {
            $assoc = [];
            while($row = $result->fetch_assoc()) {
                $assoc[] = $row;
            }
            return $assoc;
        } else {
            throw new Error($sql);
        }
    }

    /**
     * @param $array - Associative array in which key - column, value - value
     */
    public function insert($array) {
        $sql = "INSERT INTO ".$this->getTable()." (";
        foreach ($array as $key=>$value) {
            $sql .= " ".$key.",";
        }
        $sql[strlen($sql)-1] = ")";
        $sql .= " VALUES (";
        foreach ($array as $key=>$value) {
            $sql .= " '".$value."',";
        }
        $sql[strlen($sql)-1] = ")";
        $sql .= " ;";

        if ($this->dbConnect->query($sql) !== TRUE) {
            throw new Error("Error: " . $sql . "<br>" . $this->dbConnect->error);
        }
    }

    /**
     * @param $where - condition of deleting
     */
    public function delete($where) {
        $sql = "DELETE FROM " . $this->getTable() . " WHERE " . $where . " ;";
        if ($this->dbConnect->query($sql) !== TRUE) {
            throw new Error("Error: " . $sql . "<br>" . $this->dbConnect->error);
        }
    }

    /**
     * @param $array - associative array column => newValue
     * @param $where - condition for update
     */
    public function update($array, $where) {
        $sql = "UPDATE " . $this->getTable() . " SET ";
        foreach ($array as $key => $value) {
            $sql .= " " . $key . "='" . $value . "',";
        }
        $sql[strlen($sql)-1] = " ";
        $sql .= "WHERE " . $where . " ;";
        if ($this->dbConnect->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $this->dbConnect->error;
        }
    }

    //TODO: replace? merge?
}