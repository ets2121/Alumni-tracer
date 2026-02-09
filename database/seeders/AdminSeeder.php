<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = config('demo.admin.domain', 'qsu.com');
        $password = bcrypt(config('demo.admin.default_password', 'password'));

        // System Administrator
        \App\Models\User::updateOrCreate(
            ['email' => "admin@{$domain}"],
            [
                'name' => 'System Administrator',
                'password' => $password,
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Department Administrators
        $departments = \App\Models\Course::whereNotNull('department_name')
            ->distinct()
            ->pluck('department_name');

        foreach ($departments as $dept) {
            $deptSlug = strtolower(str_replace(' ', '.', $dept));
            \App\Models\User::updateOrCreate(
                ['email' => "admin.{$deptSlug}@{$domain}"],
                [
                    'name' => "{$dept} Administrator",
                    'password' => $password,
                    'role' => 'dept_admin',
                    'status' => 'active',
                    'department_name' => $dept,
                ]
            );
        }
    }
}
