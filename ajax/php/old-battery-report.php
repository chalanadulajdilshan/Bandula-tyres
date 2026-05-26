<?php
include_once '../../class/include.php';
header('Content-Type: application/json');

if (isset($_POST['action']) && $_POST['action'] === 'load_old_battery_report') {

    $from_date    = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
    $to_date      = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
    $detailed     = isset($_POST['detailed']) && $_POST['detailed'] == '1';

    $db = Database::getInstance();

    $where = " WHERE sii.old_battery_qty > 0 ";
    if ($from_date !== '') {
        $from_date_e = mysqli_real_escape_string($db->DB_CON, $from_date);
        $where .= " AND si.invoice_date >= '{$from_date_e}' ";
    }
    if ($to_date !== '') {
        $to_date_e = mysqli_real_escape_string($db->DB_CON, $to_date);
        $where .= " AND si.invoice_date <= '{$to_date_e}' ";
    }
    if ($department_id > 0) {
        $where .= " AND si.department_id = {$department_id} ";
    }
    // Skip cancelled invoices
    $where .= " AND (si.is_cancel IS NULL OR si.is_cancel = 0) ";

    if ($detailed) {
        $query = "
            SELECT
                si.id AS invoice_id,
                si.invoice_no,
                si.invoice_date,
                si.customer_name,
                si.customer_mobile,
                si.customer_address,
                si.vehicle_no AS inv_vehicle_no,
                si.payment_type,
                si.grand_total AS invoice_grand_total,
                dm.name AS department_name,
                sii.item_name,
                sii.quantity AS new_battery_qty,
                sii.list_price AS new_battery_list_price,
                sii.price AS new_battery_selling_price,
                sii.total AS new_battery_total,
                sii.old_battery_qty,
                sii.old_battery_price,
                (sii.old_battery_qty * sii.old_battery_price) AS line_total
            FROM `sales_invoice_items` sii
            INNER JOIN `sales_invoice` si ON sii.invoice_id = si.id
            LEFT JOIN `department_master` dm ON si.department_id = dm.id
            {$where}
            ORDER BY si.invoice_date DESC, si.id DESC, sii.id ASC
        ";
    } else {
        $query = "
            SELECT
                si.id AS invoice_id,
                si.invoice_no,
                si.invoice_date,
                si.customer_name,
                dm.name AS department_name,
                SUM(sii.old_battery_qty) AS total_qty,
                SUM(sii.old_battery_qty * sii.old_battery_price) AS total_amount
            FROM `sales_invoice_items` sii
            INNER JOIN `sales_invoice` si ON sii.invoice_id = si.id
            LEFT JOIN `department_master` dm ON si.department_id = dm.id
            {$where}
            GROUP BY si.id
            ORDER BY si.invoice_date DESC, si.id DESC
        ";
    }

    $result = $db->readQuery($query);
    $rows = [];
    $grand_qty = 0;
    $grand_amount = 0;

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Strip ARN/DEPT/PRE-INV metadata from item_name for detailed view
            if ($detailed && isset($row['item_name'])) {
                $name = $row['item_name'];
                if (strpos($name, '|ARN:') !== false) {
                    $name = trim(explode('|ARN:', $name)[0]);
                } elseif (strpos($name, '|PRE-INV') !== false) {
                    $name = trim(explode('|PRE-INV', $name)[0]);
                }
                $row['item_name'] = $name;

                $grand_qty += (float)$row['old_battery_qty'];
                $grand_amount += (float)$row['line_total'];
            } else {
                $grand_qty += (float)$row['total_qty'];
                $grand_amount += (float)$row['total_amount'];
            }

            $rows[] = $row;
        }
    }

    echo json_encode([
        'status'       => 'success',
        'detailed'     => $detailed,
        'data'         => $rows,
        'grand_qty'    => $grand_qty,
        'grand_amount' => $grand_amount,
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;
