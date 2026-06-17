<?php

class BatteryCharging
{
    public $id;
    public $invoice_no;
    public $bill_date;
    public $customer_name;
    public $address;
    public $deposit_amount;
    public $loan_hire_per_day;
    public $ready_date;
    public $make;
    public $voltage;
    public $battery_no;
    public $loan_battery;
    public $acid;
    public $repairs;
    public $charging;
    public $total;
    public $company_id;
    public $created_by;
    public $status;
    public $created_at;

    public function __construct($id = null)
    {
        if ($id) {
            $db = Database::getInstance();
            $query = "SELECT * FROM `battery_charging` WHERE `id` = " . (int) $id;
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id                = $result['id'];
                $this->invoice_no        = $result['invoice_no'];
                $this->bill_date         = $result['bill_date'];
                $this->customer_name     = $result['customer_name'];
                $this->address           = $result['address'];
                $this->deposit_amount    = $result['deposit_amount'];
                $this->loan_hire_per_day = $result['loan_hire_per_day'];
                $this->ready_date        = $result['ready_date'];
                $this->make              = $result['make'];
                $this->voltage           = $result['voltage'];
                $this->battery_no        = $result['battery_no'];
                $this->loan_battery      = $result['loan_battery'];
                $this->acid              = $result['acid'];
                $this->repairs           = $result['repairs'];
                $this->charging          = $result['charging'];
                $this->total             = $result['total'];
                $this->company_id        = $result['company_id'];
                $this->created_by        = $result['created_by'];
                $this->status            = $result['status'];
                $this->created_at        = $result['created_at'];
            }
        }
    }

    // Generate next invoice number in C/001 format
    public function nextInvoiceNo()
    {
        $db = Database::getInstance();
        $query = "SELECT MAX(`id`) AS max_id FROM `battery_charging`";
        $row = mysqli_fetch_array($db->readQuery($query));
        $next = ((int) ($row['max_id'] ?? 0)) + 1;
        return 'C/' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    private function esc($v)
    {
        $db = Database::getInstance();
        return mysqli_real_escape_string($db->DB_CON, (string) $v);
    }

    public function create()
    {
        $db = Database::getInstance();

        if (empty($this->invoice_no)) {
            $this->invoice_no = $this->nextInvoiceNo();
        }

        $query = "INSERT INTO `battery_charging` (
            `invoice_no`, `bill_date`, `customer_name`, `address`,
            `deposit_amount`, `loan_hire_per_day`, `ready_date`,
            `make`, `voltage`, `battery_no`, `loan_battery`,
            `acid`, `repairs`, `charging`, `total`,
            `company_id`, `created_by`, `status`, `created_at`
        ) VALUES (
            '" . $this->esc($this->invoice_no) . "',
            " . (empty($this->bill_date) ? "NULL" : "'" . $this->esc($this->bill_date) . "'") . ",
            '" . $this->esc($this->customer_name) . "',
            '" . $this->esc($this->address) . "',
            '" . (float) $this->deposit_amount . "',
            '" . (float) $this->loan_hire_per_day . "',
            " . (empty($this->ready_date) ? "NULL" : "'" . $this->esc($this->ready_date) . "'") . ",
            '" . $this->esc($this->make) . "',
            '" . $this->esc($this->voltage) . "',
            '" . $this->esc($this->battery_no) . "',
            '" . $this->esc($this->loan_battery) . "',
            '" . (float) $this->acid . "',
            '" . (float) $this->repairs . "',
            '" . (float) $this->charging . "',
            '" . (float) $this->total . "',
            " . ((int) $this->company_id) . ",
            " . ((int) $this->created_by) . ",
            1,
            NOW()
        )";

        $result = $db->readQuery($query);
        if ($result) {
            return mysqli_insert_id($db->DB_CON);
        }
        return false;
    }

    public function update()
    {
        $db = Database::getInstance();
        $query = "UPDATE `battery_charging` SET
            `bill_date`         = " . (empty($this->bill_date) ? "NULL" : "'" . $this->esc($this->bill_date) . "'") . ",
            `customer_name`     = '" . $this->esc($this->customer_name) . "',
            `address`           = '" . $this->esc($this->address) . "',
            `deposit_amount`    = '" . (float) $this->deposit_amount . "',
            `loan_hire_per_day` = '" . (float) $this->loan_hire_per_day . "',
            `ready_date`        = " . (empty($this->ready_date) ? "NULL" : "'" . $this->esc($this->ready_date) . "'") . ",
            `make`              = '" . $this->esc($this->make) . "',
            `voltage`           = '" . $this->esc($this->voltage) . "',
            `battery_no`        = '" . $this->esc($this->battery_no) . "',
            `loan_battery`      = '" . $this->esc($this->loan_battery) . "',
            `acid`              = '" . (float) $this->acid . "',
            `repairs`           = '" . (float) $this->repairs . "',
            `charging`          = '" . (float) $this->charging . "',
            `total`             = '" . (float) $this->total . "'
            WHERE `id` = '" . (int) $this->id . "'";

        return $db->readQuery($query) ? true : false;
    }

    public function delete()
    {
        $db = Database::getInstance();
        $query = "DELETE FROM `battery_charging` WHERE `id` = '" . (int) $this->id . "'";
        return $db->readQuery($query);
    }

    public function all()
    {
        $db = Database::getInstance();
        $query = "SELECT * FROM `battery_charging` ORDER BY `id` DESC";
        $result = $db->readQuery($query);
        $rows = [];
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
