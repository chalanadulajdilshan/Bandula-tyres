<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$user_id = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
$company_id = 0;
if ($user_id) {
    $u = new User($user_id);
    $company_id = (int) $u->company_id;
}

function fill(BatteryCharging $bc) {
    $bc->bill_date         = $_POST['bill_date'] ?? null;
    $bc->customer_name     = $_POST['customer_name'] ?? '';
    $bc->address           = $_POST['address'] ?? '';
    $bc->deposit_amount    = $_POST['deposit_amount'] ?? 0;
    $bc->loan_hire_per_day = $_POST['loan_hire_per_day'] ?? 0;
    $bc->ready_date        = $_POST['ready_date'] ?? null;
    $bc->make              = $_POST['make'] ?? '';
    $bc->voltage           = $_POST['voltage'] ?? '';
    $bc->battery_no        = $_POST['battery_no'] ?? '';
    $bc->loan_battery      = $_POST['loan_battery'] ?? '';
    $bc->acid              = $_POST['acid'] ?? 0;
    $bc->repairs           = $_POST['repairs'] ?? 0;
    $bc->charging          = $_POST['charging'] ?? 0;
    $bc->total             = $_POST['total'] ?? 0;
}

if (isset($_POST['create'])) {
    $BC = new BatteryCharging(null);
    fill($BC);
    $BC->invoice_no = $_POST['invoice_no'] ?? '';
    $BC->company_id = $company_id;
    $BC->created_by = $user_id;

    $newId = $BC->create();
    if ($newId) {
        echo json_encode(["status" => "success", "id" => $newId, "invoice_no" => $BC->invoice_no]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit();
}

if (isset($_POST['update'])) {
    $BC = new BatteryCharging($_POST['id']);
    if (!$BC->id) {
        echo json_encode(["status" => "error", "msg" => "Not found"]);
        exit();
    }
    fill($BC);
    $ok = $BC->update();
    echo json_encode(["status" => $ok ? "success" : "error"]);
    exit();
}

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $BC = new BatteryCharging($_POST['id']);
    $ok = $BC->delete();
    echo json_encode(["status" => $ok ? "success" : "error"]);
    exit();
}

if (isset($_POST['next_invoice'])) {
    $BC = new BatteryCharging(null);
    echo json_encode(["status" => "success", "invoice_no" => $BC->nextInvoiceNo()]);
    exit();
}
