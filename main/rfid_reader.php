<?php
// rfid_reader.php
class RFIDReader {
    private $reader;
    
    public function __construct() {
        // Initialize RFID reader
        exec('python3 -c "from mfrc522 import SimpleMFRC522; reader = SimpleMFRC522()"');
    }
    
    public function readCard() {
        // Read RFID card using Python script
        $output = [];
        exec('python3 read_rfid.py', $output);
        
        if (!empty($output)) {
            return json_decode($output[0], true);
        }
        
        return null;
    }
}
?>