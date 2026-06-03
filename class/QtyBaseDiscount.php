<?php

class QtyBaseDiscount
{
    public $id;
    public $brand_id;
    public $period_month;
    public $period_year;
    public $qty;
    public $qty_max;
    public $net_discount;
    public $created_at;
    public $updated_at;

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `qty_base_discount` WHERE `id` = " . (int) $id;
            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->brand_id = $result['brand_id'];
                $this->period_month = $result['period_month'];
                $this->period_year = $result['period_year'];
                $this->qty = $result['qty'];
                $this->qty_max = $result['qty_max'];
                $this->net_discount = $result['net_discount'];
                $this->created_at = $result['created_at'];
                $this->updated_at = $result['updated_at'];
            }
        }
    }

    // Create new record
    public function create()
    {
        $query = "INSERT INTO `qty_base_discount` (`brand_id`, `period_month`, `period_year`, `qty`, `qty_max`, `net_discount`, `created_at`, `updated_at`) 
                  VALUES (
                    '{$this->brand_id}', 
                    '{$this->period_month}', 
                    '{$this->period_year}',
                    '{$this->qty}',
                    '{$this->qty_max}',
                    '{$this->net_discount}',
                    NOW(),
                    NOW()
                  )";
        $db = Database::getInstance();
        return $db->readQuery($query) ? mysqli_insert_id($db->DB_CON) : false;
    }

    // Update existing record
    public function update()
    {
        $query = "UPDATE `qty_base_discount` 
                  SET 
                    `brand_id` = '{$this->brand_id}', 
                    `period_month` = '{$this->period_month}', 
                    `period_year` = '{$this->period_year}',
                    `qty` = '{$this->qty}',
                    `qty_max` = '{$this->qty_max}',
                    `net_discount` = '{$this->net_discount}',
                    `updated_at` = NOW()
                  WHERE `id` = '{$this->id}'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    // Delete record
    public function delete()
    {
        $query = "DELETE FROM `qty_base_discount` WHERE `id` = '{$this->id}'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    // Get all records
    public function all()
    {
        $query = "SELECT * FROM `qty_base_discount` ORDER BY id ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }
}
