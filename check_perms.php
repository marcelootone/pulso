<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = Spatie\Permission\Models\Role::where('name', 'Gestor')->first();
if ($role) {
    dump($role->permissions->pluck('name'));
} else {
    echo "Role Gestor not found";
}
