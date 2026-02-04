<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasDepartmentIsolation
{
    /**
     * Request-level cache for department context to avoid recursion and redundant queries.
     */
    protected static function getDepartmentContext()
    {
        if (app()->runningInConsole())
            return null;

        static $deptContext = null;
        if ($deptContext !== null)
            return $deptContext;

        // CRITICAL: Set to false as a recursion sentinel.
        // If getting the ID or the User triggers a query that uses this trait,
        // it will see 'false' and bypass the scope, breaking the recursion loop.
        $deptContext = false;

        try {
            // Try to get ID from session directly to avoid Guard re-entry if possible
            $sessionKey = Auth::getName() ?? '';
            $userId = session()->get($sessionKey) ?: Auth::id();

            if (!$userId)
                return $deptContext = false;

            // Fetch user WITHOUT global scopes to break recursion and improve performance
            $user = \App\Models\User::withoutGlobalScopes()->find($userId);

            if ($user && ($user->role === 'dept_admin' || $user->role === 'admin')) {
                return $deptContext = [
                    'id' => $user->id,
                    'role' => $user->role,
                    'dept' => $user->department_name
                ];
            }
        } catch (\Throwable $e) {
            // Fallback to false on any error during context resolution
            return $deptContext = false;
        }

        return $deptContext = false;
    }

    public static function bootHasDepartmentIsolation()
    {
        static::addGlobalScope('department', function (Builder $builder) {
            $ctx = self::getDepartmentContext();

            // Only apply isolation for Department Administrators
            if ($ctx && $ctx['role'] === 'dept_admin') {
                $table = $builder->getModel()->getTable();

                if ($table === 'users') {
                    // Optimized direct column filtering (O(log n) with index)
                    $builder->where(function ($q) use ($ctx) {
                        $q->where('id', $ctx['id'])
                            ->orWhere(function ($sq) use ($ctx) {
                                $sq->where('role', 'alumni')
                                    ->where('department_name', $ctx['dept']);
                            });
                    });
                } else {
                    $builder->where($table . '.department_name', $ctx['dept']);
                }
            }
        });

        static::creating(function ($model) {
            $ctx = self::getDepartmentContext();
            if ($ctx && $ctx['role'] === 'dept_admin') {
                $tableName = $model->getTable();
                if (Schema::hasColumn($tableName, 'department_name')) {
                    $model->department_name = $ctx['dept'];
                }
            }
        });
    }

    public function scopeForCurrentDepartment($query)
    {
        $ctx = self::getDepartmentContext();
        if ($ctx && $ctx['role'] === 'dept_admin') {
            return $query->where($this->getTable() . '.department_name', $ctx['dept']);
        }
        return $query;
    }
}
