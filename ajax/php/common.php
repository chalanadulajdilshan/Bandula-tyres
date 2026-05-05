<?php

include '../../class/include.php';
include '../../auth.php';


if (isset($_POST['action']) && $_POST['action'] === 'get_invoice_id_by_type') {

    $payment_type = trim($_POST['payment_type']); // Trim to remove any whitespace
    $DOCUMENT_TRACKING = new DocumentTracking(1); // Use the correct document tracking ID (1)


    if ($payment_type !== '1' && $payment_type !== '2') {
        echo json_encode(['error' => true, 'message' => 'Invalid payment type']);
        exit;
    }

    $lastNumber = max((int)$DOCUMENT_TRACKING->cash_id, (int)$DOCUMENT_TRACKING->credit_id);
    $invoiceNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    $invoice_id = 'B' . $invoiceNumber;

    echo json_encode(['invoice_id' => $invoice_id]);
    exit;
}
