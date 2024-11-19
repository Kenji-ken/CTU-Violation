<?php
// read_rfid.php
require_once 'rfid_reader.php';

header('Content-Type: application/json');

$reader = new RFIDReader();
$result = $reader->readCard();

if ($result) {
    echo json_encode($result);
} else {
    echo json_encode([
        "card_present" => false,
        "error" => "Failed to read card"
    ]);
}
?>