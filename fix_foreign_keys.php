<!-- fix_foreign_keys.php -->
 <?php
// fix_foreign_keys.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contract_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Fixing foreign keys...\n";

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// 1. Fix contracts table
echo "1. Fixing contracts table...\n";
$result = $conn->query("DESCRIBE contracts");
$has_created_by = false;
$created_by_type = '';
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'created_by') {
        $has_created_by = true;
        $created_by_type = $row['Type'];
        break;
    }
}

if ($has_created_by) {
    echo "   - created_by column exists (Type: $created_by_type)\n";
    
    // Try to add foreign key
    $sql = "ALTER TABLE contracts ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ Foreign key added to contracts\n";
    } else {
        echo "   ✗ Error adding foreign key to contracts: " . $conn->error . "\n";
    }
} else {
    echo "   ✗ created_by column not found in contracts table\n";
}

// 2. Fix activity_logs table
echo "\n2. Fixing activity_logs table...\n";
$result = $conn->query("DESCRIBE activity_logs");
$has_contract_id = false;
$contract_id_type = '';
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'contract_id') {
        $has_contract_id = true;
        $contract_id_type = $row['Type'];
        break;
    }
}

if ($has_contract_id) {
    echo "   - contract_id column exists (Type: $contract_id_type)\n";
    
    // Try to add foreign key
    $sql = "ALTER TABLE activity_logs ADD FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ Foreign key added to activity_logs\n";
    } else {
        echo "   ✗ Error adding foreign key to activity_logs: " . $conn->error . "\n";
    }
} else {
    echo "   ✗ contract_id column not found in activity_logs table\n";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "\n✅ Fix complete!\n";

$conn->close();
?>