<?php

class LoanBattery
{
    public $id;
    public $name;
    public $make;
    public $voltage;
    public $battery_no;
    public $is_active;
    public $created_at;

    public function __construct($id = null)
    {
        if ($id) {
            $db = Database::getInstance();
            $query = "SELECT * FROM `loan_batteries` WHERE `id` = " . (int) $id;
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id         = $result['id'];
                $this->name       = $result['name'];
                $this->make       = $result['make'];
                $this->voltage    = $result['voltage'];
                $this->battery_no = $result['battery_no'];
                $this->is_active  = $result['is_active'];
                $this->created_at = $result['created_at'];
            }
        }
    }

    private function esc($v)
    {
        $db = Database::getInstance();
        return mysqli_real_escape_string($db->DB_CON, (string) $v);
    }

    public function create()
    {
        $db = Database::getInstance();
        $query = "INSERT INTO `loan_batteries` (`name`, `make`, `voltage`, `battery_no`, `is_active`, `created_at`)
                  VALUES (
                    '" . $this->esc($this->name) . "',
                    '" . $this->esc($this->make) . "',
                    '" . $this->esc($this->voltage) . "',
                    '" . $this->esc($this->battery_no) . "',
                    '" . (int) $this->is_active . "',
                    NOW()
                  )";
        return $db->readQuery($query) ? mysqli_insert_id($db->DB_CON) : false;
    }

    public function update()
    {
        $db = Database::getInstance();
        $query = "UPDATE `loan_batteries` SET
                    `name`       = '" . $this->esc($this->name) . "',
                    `make`       = '" . $this->esc($this->make) . "',
                    `voltage`    = '" . $this->esc($this->voltage) . "',
                    `battery_no` = '" . $this->esc($this->battery_no) . "',
                    `is_active`  = '" . (int) $this->is_active . "'
                  WHERE `id` = '" . (int) $this->id . "'";
        return $db->readQuery($query) ? true : false;
    }

    public function delete()
    {
        $db = Database::getInstance();
        $query = "DELETE FROM `loan_batteries` WHERE `id` = '" . (int) $this->id . "'";
        return $db->readQuery($query);
    }

    public function all()
    {
        $db = Database::getInstance();
        $query = "SELECT * FROM `loan_batteries` ORDER BY `name` ASC";
        $result = $db->readQuery($query);
        $rows = [];
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function activeBatteries()
    {
        $db = Database::getInstance();
        $query = "SELECT * FROM `loan_batteries` WHERE `is_active` = 1 ORDER BY `name` ASC";
        $result = $db->readQuery($query);
        $rows = [];
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
