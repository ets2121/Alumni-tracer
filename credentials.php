<?php
/**
 * Standalone Credentials Script
 * Fetches current system credentials directly from the database.
 */

// 1. Configuration & Connection
// Since this is standalone, we'll try to read .env or use defaults
$envPath = __DIR__ . '/.env';
$dbConfig = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'alumni_system',
    'user' => 'root',
    'password' => ''
];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2) + [NULL, NULL];
        if ($name !== NULL) {
            $value = trim($value, '"\' ');
            switch ($name) {
                case 'DB_HOST': $dbConfig['host'] = $value; break;
                case 'DB_PORT': $dbConfig['port'] = $value; break;
                case 'DB_DATABASE': $dbConfig['database'] = $value; break;
                case 'DB_USERNAME': $dbConfig['user'] = $value; break;
                case 'DB_PASSWORD': $dbConfig['password'] = $value; break;
            }
        }
    }
}

try {
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// 2. Data Fetching
// Fetch Admins
$stmtAdmins = $pdo->query("SELECT name, email, role, department_name as department FROM users WHERE role IN ('admin', 'dept_admin')");
$admins = $stmtAdmins->fetchAll();

// Fetch Verified Alumni
$verifiedAlumni = [];
$stmtVerified = $pdo->query("
    SELECT u.name, u.email, u.department_name as department, c.code as course 
    FROM users u
    LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
    LEFT JOIN courses c ON ap.course_id = c.id
    WHERE u.role = 'alumni' AND u.status IN ('active', 'verified')
");
while ($row = $stmtVerified->fetch()) {
    $dept = $row['department'] ?: 'Global';
    $verifiedAlumni[$dept][] = $row;
}
ksort($verifiedAlumni);

// Fetch Pending Alumni
$stmtPending = $pdo->query("
    SELECT u.name, u.email, u.department_name as department, u.status, c.code as course 
    FROM users u
    LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
    LEFT JOIN courses c ON ap.course_id = c.id
    WHERE u.role = 'alumni' AND u.status NOT IN ('active', 'verified')
");
$pendingAlumni = $stmtPending->fetchAll();

// 3. Rendering
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Credentials - Alumni System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --accent: #10b981;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: var(--bg); 
            color: var(--text); 
            font-family: 'Inter', sans-serif; 
            padding: 40px 20px;
            line-height: 1.5;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { font-family: 'Outfit', sans-serif; font-size: 2.5rem; margin-bottom: 8px; text-align: center; }
        .subtitle { color: var(--text-muted); text-align: center; margin-bottom: 40px; }
        h2 { font-family: 'Outfit', sans-serif; font-size: 1.5rem; margin: 40px 0 20px; color: var(--accent); border-bottom: 2px solid var(--border); padding-bottom: 8px; }
        h3 { font-size: 1.1rem; margin: 24px 0 12px; color: var(--text); display: flex; align-items: center; gap: 8px; }
        h3::before { content: ""; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; }
        
        .table-container { 
            background: var(--card); 
            border-radius: 12px; 
            overflow: hidden; 
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(51, 65, 85, 0.5); padding: 12px 16px; font-weight: 600; font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 12px 16px; border-top: 1px solid var(--border); font-size: 0.9375rem; }
        tr:hover { background: rgba(255, 255, 255, 0.02); }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-admin { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .badge-dept { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .badge-alumni { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        
        code { font-family: monospace; background: #000; padding: 2px 6px; border-radius: 4px; color: var(--accent); }
    </style>
</head>
<body>
    <div class="container">
        <h1>System Credentials</h1>
        <p class="subtitle">Live data fetched from current database (Last Updated: <?= date('Y-m-d H:i') ?>)</p>

        <h2>1. Administrative Accounts</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['name']) ?></td>
                        <td><code><?= htmlspecialchars($admin['email']) ?></code></td>
                        <td>
                            <span class='badge <?= $admin['role'] === 'admin' ? 'badge-admin' : 'badge-dept' ?>'>
                                <?= $admin['role'] === 'admin' ? 'System Admin' : 'Dept Admin' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($admin['department'] ?: 'Global') ?></td>
                        <td><code>password</code></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2>2. Verified Alumni</h2>
        <?php foreach ($verifiedAlumni as $dept => $alumnis): ?>
            <h3><?= htmlspecialchars($dept) ?></h3>
            <div class='table-container'>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnis as $alumni): ?>
                        <tr>
                            <td><?= htmlspecialchars($alumni['name']) ?></td>
                            <td><code><?= htmlspecialchars($alumni['email']) ?></code></td>
                            <td><?= htmlspecialchars($alumni['course'] ?: 'N/A') ?></td>
                            <td><code>password</code></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <h2>3. Pending / Unverified Accounts</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingAlumni as $alumni): ?>
                    <tr>
                        <td><?= htmlspecialchars($alumni['name']) ?></td>
                        <td><code><?= htmlspecialchars($alumni['email']) ?></code></td>
                        <td><?= htmlspecialchars($alumni['department'] ?: 'Global') ?></td>
                        <td><?= htmlspecialchars($alumni['course'] ?: 'N/A') ?></td>
                        <td><span class='badge badge-pending'><?= ucfirst(htmlspecialchars($alumni['status'])) ?></span></td>
                        <td><code>password</code></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
