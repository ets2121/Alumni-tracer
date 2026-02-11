<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\File;

class GenerateCredentialsDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an HTML file with current system credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Extracting user data...');

        $users = User::withoutGlobalScopes()
            ->with(['alumniProfile.course'])
            ->get()
            ->map(function($user) {
                $course = $user->alumniProfile && $user->alumniProfile->course 
                    ? $user->alumniProfile->course->code 
                    : 'N/A';
                
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department_name ?: 'Global',
                    'status' => $user->status,
                    'course' => $course
                ];
            });

        $admins = [];
        $verifiedAlumni = [];
        $pendingAlumni = [];

        foreach ($users as $user) {
            if ($user['role'] === 'admin' || $user['role'] === 'dept_admin') {
                $admins[] = $user;
            } elseif ($user['status'] === 'active' || $user['status'] === 'verified') {
                $dept = $user['department'] ?: 'Global';
                $verifiedAlumni[$dept][] = $user;
            } else {
                $pendingAlumni[] = $user;
            }
        }

        ksort($verifiedAlumni);

        $this->info('Generating HTML content...');

        $html = $this->generateHtml($admins, $verifiedAlumni, $pendingAlumni);

        $outputPath = base_path('credentials.html');
        File::put($outputPath, $html);

        $this->info("Successfully generated credentials documentation at: {$outputPath}");
        $this->info("Total records processed: " . count($users));
    }

    private function generateHtml($admins, $verifiedAlumni, $pendingAlumni)
    {
        ob_start();
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
        <p class="subtitle">Live data generated from current database seeds (Last Updated: <?= date('Y-m-d H:i') ?>)</p>

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
                        <td><?= htmlspecialchars($admin['department']) ?></td>
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
                            <td><?= htmlspecialchars($alumni['course']) ?></td>
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
                        <td><?= htmlspecialchars($alumni['department']) ?></td>
                        <td><?= htmlspecialchars($alumni['course']) ?></td>
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
        <?php
        return ob_get_clean();
    }
}
