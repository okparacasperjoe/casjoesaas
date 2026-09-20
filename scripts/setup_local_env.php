<?php

echo "Starting Local Environment Setup...\n";

// Configuration
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = ''; // Default XAMPP/Laragon password
$dbName = 'saas_db';

// 1. Connect to MySQL Server (Create connection without DB first)
try {
    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL server.\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\nCheck your database credentials in setup_local_env.php\n");
}

// 2. Create Database
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "Database '$dbName' checked/created.\n";
} catch (PDOException $e) {
    die("Error creating database: " . $e->getMessage() . "\n");
}

// 3. Select Database
try {
    $pdo->exec("USE `$dbName`");
} catch (PDOException $e) {
    die("Error selecting database: " . $e->getMessage() . "\n");
}

// 4. Run SQL Schema Files
function runSqlFile($pdo, $file)
{
    if (!file_exists($file)) {
        echo "Warning: File $file not found. Skipping.\n";
        return;
    }
    echo "Running $file...\n";
    $sql = file_get_contents($file);
    try {
        // Split by semicolon to execute mostly correctly, or just run exec if it's capable
        // Basic split for simple dumps. For complex triggers/procedures this might be weak.
        // But for our simple schema it should be okay.
        // Actually, $pdo->exec() allows multiple statements in one go if emulation is enabled or driver supports it.
        // Let's try executing the whole block.
        $pdo->exec($sql);
        echo "Executed $file successfully.\n";
    } catch (PDOException $e) {
        // If it fails, maybe table exists. We'll show error but continue.
        echo "Note on $file: " . $e->getMessage() . "\n";
    }
}

runSqlFile($pdo, __DIR__ . '/database.sql');
runSqlFile($pdo, __DIR__ . '/database_update.sql');
runSqlFile($pdo, __DIR__ . '/billing_update.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoeERP/install.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoePay/install.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoeAcademy/install.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoeMail/install.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoeSupport/install.sql');
runSqlFile($pdo, __DIR__ . '/../app/modules/CasjoeLinks/install.sql');
runSqlFile($pdo, __DIR__ . '/notifications.sql');
runSqlFile($pdo, __DIR__ . '/2fa_update.sql');

// 5. Seed Default Tenant and User
try {
    // Check if tenant exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tenants WHERE subdomain = ?");
    $stmt->execute(['localhost']);
    if ($stmt->fetchColumn() == 0) {
        // Create Tenant
        $stmt = $pdo->prepare("INSERT INTO tenants (name, subdomain, domain) VALUES (?, ?, ?)");
        $stmt->execute(['Local Dev', 'localhost', 'localhost']);
        $tenantId = $pdo->lastInsertId();
        echo "Created default tenant 'localhost' (ID: $tenantId).\n";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM tenants WHERE subdomain = ?");
        $stmt->execute(['localhost']);
        $tenantId = $stmt->fetchColumn();
        echo "Default tenant 'localhost' already exists (ID: $tenantId).\n";
    }

    // Check if admin user exists
    $adminEmail = 'admin@localhost';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND tenant_id = ?");
    $stmt->execute([$adminEmail, $tenantId]);
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (tenant_id, email, password, role, is_verified) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$tenantId, $adminEmail, $password, 'admin']);
        echo "Created default admin user: $adminEmail / password\n";
    } else {
        echo "Default admin user '$adminEmail' already exists.\n";
    }



    // Check if normal user exists
    $userEmail = 'user@localhost';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND tenant_id = ?");
    $stmt->execute([$userEmail, $tenantId]);
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (tenant_id, email, password, role, is_verified) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$tenantId, $userEmail, $password, 'user']);
        echo "Created default normal user: $userEmail / password\n";
    } else {
        echo "Default normal user '$userEmail' already exists.\n";
    }


    // Enable All Modules
    $modulesToEnable = [
        ['name' => 'Casjoe BOS', 'slug' => 'casjoe-erp', 'description' => 'Core ERP Module'],
        ['name' => 'Casjoe Pay', 'slug' => 'casjoe-pay', 'description' => 'Payment Suite'],
        ['name' => 'Casjoe Business School', 'slug' => 'casjoe-academy', 'description' => 'LMS & Courses'],
        ['name' => 'Casjoe Mail', 'slug' => 'casjoe-mail', 'description' => 'Email Marketing'],
        ['name' => 'Casjoe Support', 'slug' => 'casjoe-support', 'description' => 'Helpdesk & Ticketing'],
        ['name' => 'Casjoe Links', 'slug' => 'casjoe-links', 'description' => 'Bio Pages, URL Shortener & QR Codes']
    ];

    foreach ($modulesToEnable as $mod) {
        $moduleSlug = $mod['slug'];

        // 1. Ensure Module Exists
        $stmt = $pdo->prepare("SELECT id FROM modules WHERE slug = ?");
        $stmt->execute([$moduleSlug]);
        $moduleId = $stmt->fetchColumn();

        if (!$moduleId) {
            $stmt = $pdo->prepare("INSERT INTO modules (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([$mod['name'], $mod['slug'], $mod['description']]);
            $moduleId = $pdo->lastInsertId();
            echo "Created module '$moduleSlug'.\n";
        }

        // 2. Enable for Tenant
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tenant_modules WHERE tenant_id = ? AND module_id = ?");
        $stmt->execute([$tenantId, $moduleId]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO tenant_modules (tenant_id, module_id, status) VALUES (?, ?, 'enabled')");
            $stmt->execute([$tenantId, $moduleId]);
            echo "Enabled '$moduleSlug' for tenant.\n";
        } else {
            echo "Module '$moduleSlug' already enabled.\n";
        }
    }

} catch (PDOException $e) {
    die("Seeding Error: " . $e->getMessage() . "\n");
}

echo "\nSetup Complete!\n";
echo "You can now run the server using 'run_local.bat' or 'php -S localhost:8000 -t public_html'\n";
