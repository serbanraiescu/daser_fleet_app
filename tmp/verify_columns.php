<?php
try {
    $db = new PDO('sqlite:fleetlog/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $res = $db->query("PRAGMA table_info(tenants)");
    $columns = [];
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['name'];
    }
    echo "COLUMNS: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
