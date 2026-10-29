<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The Tracking screens sit behind their own "Tracking" permission. It is
 * granted to the Super Admin role here; other roles get it from
 * Admin Manage > All Admin Role, like any other permission.
 */
return new class extends Migration
{
    private const PERMISSION = 'tracking';
    private const ROLE = 'Super Admin';

    public function up(): void
    {
        $this->changePermission(function (array $permissions) {
            if (!in_array(self::PERMISSION, $permissions, true)) {
                $permissions[] = self::PERMISSION;
            }

            return $permissions;
        });
    }

    public function down(): void
    {
        $this->changePermission(
            fn (array $permissions) => array_values(array_diff($permissions, [self::PERMISSION]))
        );
    }

    private function changePermission(callable $change): void
    {
        if (!Schema::hasTable('admin_roles')) {
            return;
        }

        $role = DB::table('admin_roles')->where('name', self::ROLE)->first();
        if ($role === null) {
            return;
        }

        $permissions = json_decode((string) $role->permission, true);
        if (!is_array($permissions)) {
            return;
        }

        DB::table('admin_roles')->where('id', $role->id)->update([
            'permission' => json_encode(array_values($change($permissions))),
        ]);
    }
};
