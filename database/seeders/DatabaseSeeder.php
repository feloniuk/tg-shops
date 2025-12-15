<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in correct order
        $this->call([
            PlanSeeder::class,
            RoleAndPermissionSeeder::class,
            // Uncomment to seed demo data:
            // DemoDataSeeder::class,
        ]);

        echo "\n✓ All seeders completed successfully!\n";
        echo "To add demo data, uncomment DemoDataSeeder in DatabaseSeeder.php\n";
    }
}
