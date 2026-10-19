<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Request as CustomerRequest;
use App\Models\RequestMessage;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
            'is_approved' => true,
            'phone' => '+1 555-0199',
            'approved_at' => Carbon::now()->subMonths(3),
            'last_login' => Carbon::now(),
            'kyc_status' => 'verified',
            'kyc_verified_at' => Carbon::now()->subMonths(3),
        ]);

        echo "Admin created: admin@example.com / password\n";

        // 2. Create KYC Verified Active Customers with Message Threads
        $verifiedCustomer = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'active',
            'is_approved' => true,
            'phone' => '+1 555-0101',
            'id_document_type' => 'Passport',
            'id_document_path' => 'kyc_documents/sample_passport.png',
            'kyc_status' => 'verified',
            'kyc_verified_at' => Carbon::now()->subDays(15),
            'kyc_verified_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(16),
            'created_at' => Carbon::now()->subDays(18),
            'last_login' => Carbon::now()->subHours(2),
        ]);

        AuditLog::create([
            'user_id' => $verifiedCustomer->id,
            'admin_id' => $admin->id,
            'action' => 'KYC Verified',
            'description' => 'Administrator verified customer identity passport.',
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // Create Request with two-way conversation thread
        $req1 = CustomerRequest::create([
            'user_id' => $verifiedCustomer->id,
            'title' => 'Increase Monthly Transaction Limit',
            'description' => 'Hello team, I need to raise my account spending limit from $5,000 to $20,000 for my quarterly business procurement.',
            'type' => 'service_request',
            'status' => 'in_progress',
            'submitted_at' => Carbon::now()->subDays(3),
            'created_at' => Carbon::now()->subDays(3),
        ]);

        RequestMessage::create([
            'request_id' => $req1->id,
            'user_id' => $verifiedCustomer->id,
            'message' => 'Hi, please find my latest business bank statements as proof of revenue.',
            'is_admin' => false,
            'created_at' => Carbon::now()->subDays(3)->addHours(1),
        ]);

        RequestMessage::create([
            'request_id' => $req1->id,
            'user_id' => $admin->id,
            'message' => 'Thank you Sarah. We have received your request and our compliance team is verifying the documents. We should have an update within 24 hours.',
            'is_admin' => true,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        RequestMessage::create([
            'request_id' => $req1->id,
            'user_id' => $verifiedCustomer->id,
            'message' => 'Sounds great, thank you for the prompt update!',
            'is_admin' => false,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // 3. Customer with Pending KYC Review
        $pendingKycCustomer = User::create([
            'name' => 'Michael Chen',
            'email' => 'michael@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'pending',
            'is_approved' => false,
            'phone' => '+1 555-0102',
            'id_document_type' => 'National ID',
            'id_document_path' => 'kyc_documents/sample_national_id.png',
            'kyc_status' => 'pending',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        CustomerRequest::create([
            'user_id' => $pendingKycCustomer->id,
            'title' => 'New Customer Registration Activation',
            'description' => 'I just completed registration and uploaded my National Identity Card. Looking forward to account approval.',
            'type' => 'account_activation',
            'status' => 'pending',
            'submitted_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // 4. Customer with Rejected KYC
        $rejectedKycCustomer = User::create([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'active',
            'is_approved' => true,
            'phone' => '+1 555-0103',
            'id_document_type' => 'Driving License',
            'id_document_path' => 'kyc_documents/sample_license.png',
            'kyc_status' => 'rejected',
            'kyc_notes' => 'The uploaded photo was blurry and corners were cut off. Please re-upload a clear scanned copy.',
            'approved_at' => Carbon::now()->subDays(8),
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // 5. Customer with Inactive Status
        $inactiveCustomer = User::create([
            'name' => 'Emily Watson',
            'email' => 'emily@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'inactive',
            'is_approved' => true,
            'phone' => '+1 555-0104',
            'approved_at' => Carbon::now()->subDays(25),
            'created_at' => Carbon::now()->subDays(30),
            'last_login' => Carbon::now()->subDays(20),
            'kyc_status' => 'unsubmitted',
        ]);

        // 6. Generate distributed historical customers for 30-day Chart.js trend curves
        $names = [
            'David Miller', 'Jessica Taylor', 'Daniel Anderson', 'Sophia Martinez',
            'James Thomas', 'Olivia Jackson', 'Liam White', 'Emma Harris',
            'Noah Martin', 'Ava Thompson', 'Lucas Garcia', 'Mia Robinson',
            'Benjamin Clark', 'Amelia Lewis', 'Ethan Lee', 'Harper Walker'
        ];

        foreach ($names as $idx => $name) {
            $daysAgo = rand(1, 28);
            $isApproved = ($idx % 3 !== 0); // 2 out of 3 approved
            $status = $isApproved ? 'active' : 'pending';
            $regDate = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 20));
            $appDate = $isApproved ? $regDate->copy()->addHours(rand(2, 48)) : null;

            $cust = User::create([
                'name' => $name,
                'email' => 'user' . ($idx + 1) . '@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'status' => $status,
                'is_approved' => $isApproved,
                'phone' => '+1 555-02' . str_pad($idx, 2, '0', STR_PAD_LEFT),
                'kyc_status' => $isApproved ? ($idx % 2 === 0 ? 'verified' : 'pending') : 'unsubmitted',
                'id_document_type' => $isApproved ? 'National ID' : null,
                'approved_at' => $appDate,
                'created_at' => $regDate,
                'last_login' => $isApproved ? Carbon::now()->subDays(rand(0, 5)) : null,
            ]);

            // Add sample requests for some users
            if ($idx % 2 === 0) {
                $reqType = ['service_request', 'information_change', 'other'][array_rand(['service_request', 'information_change', 'other'])];
                $reqStatus = ['pending', 'approved', 'in_progress'][array_rand(['pending', 'approved', 'in_progress'])];
                
                CustomerRequest::create([
                    'user_id' => $cust->id,
                    'title' => ucfirst(str_replace('_', ' ', $reqType)) . ' for ' . $name,
                    'description' => 'Customer inquiry and service support ticket submitted on ' . $regDate->format('M d, Y'),
                    'type' => $reqType,
                    'status' => $reqStatus,
                    'submitted_at' => $regDate,
                    'processed_at' => $reqStatus === 'approved' ? $regDate->copy()->addHours(rand(1, 12)) : null,
                    'processed_by' => $reqStatus === 'approved' ? $admin->id : null,
                    'created_at' => $regDate,
                ]);
            }
        }

        echo "Seeding completed successfully! Total users: " . User::count() . "\n";
    }
}