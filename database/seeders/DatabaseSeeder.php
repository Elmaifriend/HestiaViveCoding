<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Announcement;
use App\Models\GateEntry;
use App\Models\Incident;
use App\Models\Invitation;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles & Users
        $admin = User::firstOrCreate([
            'email' => 'admin@hestia.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $guard = User::firstOrCreate([
            'email' => 'guard@hestia.com',
        ], [
            'name' => 'Guard User',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Guard,
        ]);

        // Create 10 Residents
        $residents = User::factory(10)->create([
            'role' => \App\Enums\UserRole::Resident,
        ]);

        // 2. Facilities Module
        // Create 5 Amenities
        $amenities = Amenity::factory(5)->create();

        // Create Reservations for residents
        foreach ($residents as $resident) {
            Reservation::factory(2)->create([
                'resident_id' => $resident->id,
                'amenity_id' => $amenities->random()->id,
            ]);
        }

        // 3. Community Module
        // Create Announcements
        Announcement::factory(5)->create();

        // Create Marketplace Products
        foreach ($residents as $resident) {
            Product::factory(rand(0, 3))->create([
                'resident_id' => $resident->id,
            ]);
        }

        // 4. Security & Access Module
        // Create Invitations (QR)
        foreach ($residents as $resident) {
            Invitation::factory(3)->create([
                'resident_id' => $resident->id,
            ]);
        }

        // Create Gate Entries
        foreach ($residents as $resident) {
            GateEntry::factory(2)->create([
                'resident_id' => $resident->id,
            ]);
        }

        // Create Incidents
        foreach ($residents as $resident) {
            Incident::factory(rand(0, 2))->create([
                'resident_id' => $resident->id,
            ]);
        }

        // Create Messages (Random conversations)
        foreach ($residents as $resident) {
            // Message to Admin
            Message::factory()->create([
                'sender_id' => $resident->id,
                'receiver_id' => $admin->id,
            ]);
            // Message from Admin
            Message::factory()->create([
                'sender_id' => $admin->id,
                'receiver_id' => $resident->id,
            ]);
        }

        // 5. Finances Module
        // Create Payments
        foreach ($residents as $resident) {
            Payment::factory(rand(1, 4))->create([
                'resident_id' => $resident->id,
            ]);
        }
    }
}
