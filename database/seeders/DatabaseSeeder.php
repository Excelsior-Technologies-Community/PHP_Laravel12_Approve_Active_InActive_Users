<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Request;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
            'is_approved' => true,
            'phone' => '1234567890',
            'approved_at' => now(),
        ]);

        echo "Admin created: admin@example.com / password\n";

        // Create Pending Customers
        for ($i = 1; $i <= 3; $i++) {
            $customer = User::create([
                'name' => 'Pending Customer ' . $i,
                'email' => 'pending' . $i . '@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'status' => 'pending',
                'is_approved' => false,
                'phone' => '12345678' . $i,
            ]);

            // Create auto activation request
            Request::create([
                'user_id' => $customer->id,
                'title' => 'Account Activation Request',
                'description' => 'New customer registration. Please review and activate account.',
                'type' => 'account_activation',
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            echo "Pending customer {$i}: pending{$i}@example.com / password\n";
        }

        // Create Active Customers with requests
        for ($i = 1; $i <= 5; $i++) {
            $customer = User::create([
                'name' => 'Active Customer ' . $i,
                'email' => 'active' . $i . '@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'status' => 'active',
                'is_approved' => true,
                'phone' => '98765432' . $i,
                'approved_at' => now()->subDays($i),
                'last_login' => now()->subHours($i),
            ]);

            // Create sample requests
            $requestTypes = ['service_request', 'information_change', 'other'];
            
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $type = $requestTypes[array_rand($requestTypes)];
                $status = ['pending', 'approved', 'rejected', 'in_progress'][array_rand(['pending', 'approved', 'rejected', 'in_progress'])];
                
                $request = Request::create([
                    'user_id' => $customer->id,
                    'title' => ucfirst(str_replace('_', ' ', $type)) . ' ' . $j,
                    'description' => 'This is a sample ' . str_replace('_', ' ', $type) . ' description for customer ' . $i,
                    'type' => $type,
                    'status' => $status,
                    'submitted_at' => now()->subDays($j),
                    'processed_at' => $status !== 'pending' ? now()->subDays($j-1) : null,
                    'processed_by' => $status !== 'pending' ? $admin->id : null,
                ]);

                // Create notification for some requests
                if (in_array($status, ['approved', 'rejected'])) {
                    Notification::create([
                        'user_id' => $customer->id,
                        'title' => 'Request ' . ucfirst($status),
                        'message' => 'Your request "' . $request->title . '" has been ' . $status,
                        'type' => 'request_update',
                        'data' => ['request_id' => $request->id],
                        'created_at' => now()->subDays($j-1),
                    ]);
                }
            }

            echo "Active customer {$i}: active{$i}@example.com / password\n";
        }

        // Create Inactive Customer
        $inactive = User::create([
            'name' => 'Inactive Customer',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'inactive',
            'is_approved' => true,
            'phone' => '5555555555',
            'approved_at' => now()->subDays(10),
            'last_login' => now()->subDays(5),
        ]);

        echo "Inactive customer: inactive@example.com / password\n";
        echo "\nSeeding completed successfully!\n";
    }
}