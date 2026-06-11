<?php

class ArnItemBarcode
{
    public $id;
    public $arn_id;
    public $arn_item_id;
    public $item_id;
    public $unit_seq;
    public $barcode;
    public $month_letter;
    public $year_code;
    public $arn_no;
    public $entry_date;
    public $is_used;
    public $created_at;

    public function __construct($id = NULL)
    {
        if ($id) {
            $db = Database::getInstance();
            $query = "SELECT * FROM `arn_item_barcodes` WHERE `id` = '" . (int)$id . "'";
            $result = $db->readQuery($query);
            if ($row = mysqli_fetch_assoc($result)) {
                foreach ($row as $k => $v) {
                    $this->$k = $v;
                }
            }
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        $arn_no = $db->escapeString($this->arn_no);
        $barcode = $db->escapeString($this->barcode);
        $query = "INSERT INTO `arn_item_barcodes`
            (`arn_id`,`arn_item_id`,`item_id`,`unit_seq`,`barcode`,`month_letter`,`year_code`,`arn_no`,`entry_date`,`created_at`)
            VALUES
            ('{$this->arn_id}','{$this->arn_item_id}','{$this->item_id}','{$this->unit_seq}','{$barcode}','{$this->month_letter}','{$this->year_code}','{$arn_no}','{$this->entry_date}',NOW())";

        if ($db->readQuery($query)) {
            return mysqli_insert_id($db->DB_CON);
        }
        return false;
    }

    public static function monthLetter($monthNumber)
    {
        $monthNumber = (int)$monthNumber;
        if ($monthNumber < 1 || $monthNumber > 12) {
            return '';
        }
        return chr(ord('A') + ($monthNumber - 1));
    }

    public static function buildBarcode($arn_no, $arn_item_id, $unit_seq, $entry_date)
    {
        $ts = strtotime($entry_date);
        if (!$ts) {
            $ts = time();
        }
        $month = self::monthLetter(date('n', $ts));
        $year  = date('y', $ts);
        $safeArn = preg_replace('/[^A-Z0-9]/i', '', $arn_no);
        $seq = str_pad((string)$unit_seq, 2, '0', STR_PAD_LEFT);
        return $month . $year . '-' . $safeArn . '-' . (int)$arn_item_id . '-' . $seq;
    }

    public static function getByArnId($arn_id)
    {
        $db = Database::getInstance();
        $arn_id = (int)$arn_id;
        $query = "SELECT b.*, im.code AS item_code, im.name AS item_name
                  FROM `arn_item_barcodes` b
                  LEFT JOIN `item_master` im ON im.id = b.item_id
                  WHERE b.`arn_id` = '{$arn_id}'
                  ORDER BY b.`arn_item_id` ASC, b.`unit_seq` ASC";
        $result = $db->readQuery($query);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
