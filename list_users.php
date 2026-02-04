<?php
use App\Models\User;
foreach (User::all() as $u) {
    echo "ID: {$u->id} | Role: {$u->role} | Dept: " . ($u->department_name ?? 'NONE') . PHP_EOL;
}
