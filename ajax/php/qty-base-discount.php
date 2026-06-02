<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Create a new quantity base discount
if (isset($_POST['create'])) {

    $DISCOUNT = new QtyBaseDiscount(NULL);

    $DISCOUNT->brand_id = $_POST['brand_id'];
    $DISCOUNT->period_month = $_POST['period_month'];
    $DISCOUNT->period_year = $_POST['period_year'];
    $DISCOUNT->qty = $_POST['qty'];
    $DISCOUNT->net_discount = $_POST['net_discount'];

    $res = $DISCOUNT->create();

    if ($res) {
        echo json_encode(["status" => 'success']);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Update quantity base discount
if (isset($_POST['update'])) {

    $DISCOUNT = new QtyBaseDiscount($_POST['id']);

    $DISCOUNT->brand_id = $_POST['brand_id'];
    $DISCOUNT->period_month = $_POST['period_month'];
    $DISCOUNT->period_year = $_POST['period_year'];
    $DISCOUNT->qty = $_POST['qty'];
    $DISCOUNT->net_discount = $_POST['net_discount'];

    $res = $DISCOUNT->update();

    if ($res) {
        echo json_encode(["status" => 'success']);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Delete quantity base discount
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $DISCOUNT = new QtyBaseDiscount($_POST['id']);
    $res = $DISCOUNT->delete();

    if ($res) {
        echo json_encode(["status" => 'success']);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}
