<?php
require_once 'config/config.php';
$db = getDbInstance();

// Add special_remarks column if it doesn't exist
$columns = $db->rawQuery("DESCRIBE property_booking");
$exists = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'special_remarks') {
        $exists = true;
        break;
    }
}

if (!$exists) {
    $db->rawQuery("ALTER TABLE property_booking ADD COLUMN special_remarks TEXT AFTER guest_whatsapp");
    echo "Column 'special_remarks' added successfully.";
} else {
    echo "Column 'special_remarks' already exists.";
}
