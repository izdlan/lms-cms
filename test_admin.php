<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::where('email', 'admin@lms-olympia.com')->first();

if ($admin) {
    echo "Admin found: " . $admin->name . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Role: " . $admin->role . "\n";
    echo "Password hash: " . $admin->password . "\n";
    echo "Password check: " . (Hash::check('admin123', $admin->password) ? 'PASS' : 'FAIL') . "\n";
} else {
    echo "Admin user not found!\n";
}
