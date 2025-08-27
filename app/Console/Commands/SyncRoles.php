<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\PermissionTableSeeder;
use Database\Seeders\RoleTableSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:sync-roles {--super-admin-id=1} {--truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync role and permissions to user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $truncate = $this->option('truncate');
        $superAdminId = $this->option('super-admin-id');
        Schema::disableForeignKeyConstraints();
        try {
            if ($truncate) {
                Permission::truncate();
                Role::truncate();
            }

            // Import permissions
            Artisan::call('db:seed', [
                '--class' => PermissionTableSeeder::class,
                '--force' => true
            ]);

            // Import roles
            Artisan::call('db:seed', [
                '--class' => RoleTableSeeder::class,
                '--force' => true
            ]);

            $permission = Permission::all()->pluck('name');
            $administrator = Role::findByName('administrator')->syncPermissions($permission);
            $super_admin = Role::findByName('super_admin')->syncPermissions($permission);
            $admin = Role::findByName('admin');
            switch ($superAdminId){
                case "all":
                    User::query()->whereIn('role',['super_admin','admin'])->get()->map(function ($user) use ($admin , $super_admin) {
                        $user->syncRoles([$super_admin->name,$admin->name]);
                    });
                    break;
                default:
                    if ($user = User::query()->find($superAdminId)) {
                        $user->syncRoles([$administrator->name,$super_admin->name,$admin->name]);
                    }
            }
            Schema::enableForeignKeyConstraints();
        } catch (\Exception $exception) {
            Schema::enableForeignKeyConstraints();
            throw $exception;
        }
    }
}
