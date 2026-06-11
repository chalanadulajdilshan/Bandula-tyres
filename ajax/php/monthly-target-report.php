<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

$action = $_POST['action'] ?? '';

if ($action === 'load_report') {

    $brand_id  = (int) ($_POST['brand_id'] ?? 0);
    $from_date = $_POST['from_date'] ?? '';
    $to_date   = $_POST['to_date'] ?? '';

    if ($brand_id <= 0 || empty($from_date) || empty($to_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    $db = Database::getInstance();

    $from_safe = mysqli_real_escape_string($db->DB_CON, $from_date);
    $to_safe   = mysqli_real_escape_string($db->DB_CON, $to_date);

    // 1. Target tiers for the brand whose `period_year` (stored as a date) falls within the range
    $tier_query = "SELECT `id`, `qty`, `qty_max`, `net_discount`, `period_month`, `period_year`
                   FROM `qty_base_discount`
                   WHERE `brand_id` = {$brand_id}
                     AND DATE(`period_year`) BETWEEN '{$from_safe}' AND '{$to_safe}'
                   ORDER BY CAST(`qty` AS DECIMAL(15,2)) ASC";

    $tiers = [];
    $tres = $db->readQuery($tier_query);
    if ($tres) {
        while ($row = mysqli_fetch_assoc($tres)) {
            $tiers[] = $row;
        }
    }

    // Fallback: if no tiers fall inside range, return all configured tiers for the brand
    if (empty($tiers)) {
        $fallback_query = "SELECT `id`, `qty`, `qty_max`, `net_discount`, `period_month`, `period_year`
                           FROM `qty_base_discount`
                           WHERE `brand_id` = {$brand_id}
                           ORDER BY CAST(`qty` AS DECIMAL(15,2)) ASC";
        $fres = $db->readQuery($fallback_query);
        if ($fres) {
            while ($row = mysqli_fetch_assoc($fres)) {
                $tiers[] = $row;
            }
        }
    }

    // 2. Actual sales qty for the brand in the date range
    //    sales_invoice_items.item_code -> item_master.id -> item_master.brand
    //    Only count non-cancelled invoices within the date range.
    $sales_query = "SELECT COALESCE(SUM(sii.quantity), 0) AS sales_qty
                    FROM `sales_invoice_items` sii
                    INNER JOIN `sales_invoice` si ON si.id = sii.invoice_id
                    INNER JOIN `item_master` im   ON im.id = sii.item_code
                    WHERE im.brand = {$brand_id}
                      AND (si.is_cancel IS NULL OR si.is_cancel = 0)
                      AND DATE(si.invoice_date) BETWEEN '{$from_safe}' AND '{$to_safe}'";

    $sales_qty = 0;
    $sres = $db->readQuery($sales_query);
    if ($sres) {
        $row = mysqli_fetch_assoc($sres);
        $sales_qty = (float) ($row['sales_qty'] ?? 0);
    }

    echo json_encode([
        'status'    => 'success',
        'tiers'     => $tiers,
        'sales_qty' => $sales_qty,
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
