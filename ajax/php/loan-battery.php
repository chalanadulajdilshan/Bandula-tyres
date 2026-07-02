<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Create
if (isset($_POST['create'])) {
    $LB = new LoanBattery(NULL);
    $LB->name       = $_POST['name'] ?? '';
    $LB->make       = $_POST['make'] ?? '';
    $LB->voltage    = $_POST['voltage'] ?? '';
    $LB->battery_no = $_POST['battery_no'] ?? '';
    $LB->is_active  = isset($_POST['activeStatus']) ? 1 : 0;

    echo json_encode(["status" => $LB->create() ? 'success' : 'error']);
    exit();
}

// Update
if (isset($_POST['update'])) {
    $LB = new LoanBattery($_POST['loan_battery_id']);
    if (!$LB->id) {
        echo json_encode(["status" => 'error', "msg" => "Not found"]);
        exit();
    }
    $LB->name       = $_POST['name'] ?? '';
    $LB->make       = $_POST['make'] ?? '';
    $LB->voltage    = $_POST['voltage'] ?? '';
    $LB->battery_no = $_POST['battery_no'] ?? '';
    $LB->is_active  = isset($_POST['activeStatus']) ? 1 : 0;

    echo json_encode(["status" => $LB->update() ? 'success' : 'error']);
    exit();
}

// Delete
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $LB = new LoanBattery($_POST['id']);
    echo json_encode(["status" => $LB->delete() ? 'success' : 'error']);
    exit();
}
