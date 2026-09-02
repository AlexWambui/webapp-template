<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Product\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductCategorySeeder extends Seeder
{
    private array $categories = [
        [
            'name' => 'Paving Block Moulds',
            'sort_order' => 1,
        ],
        [
            'name' => 'Ventilation Moulds',
            'sort_order' => 2,
        ],
        [
            'name' => 'Balustrades & Columns',
            'sort_order' => 3,
        ],
        [
            'name' => 'Tile Moulds',
            'sort_order' => 4,
        ],
        [
            'name' => 'Cladding Moulds',
            'sort_order' => 5,
        ],
        [
            'name' => 'Flower Pot Moulds',
            'sort_order' => 6,
        ],
        [
            'name' => 'Stamp Moulds',
            'sort_order' => 7,
        ],
        [
            'name' => 'Machinery & Equipment',
            'sort_order' => 8,
        ],
        [
            'name' => 'Pigments & Additives',
            'sort_order' => 9,
        ],
        [
            'name' => 'Garden & Fencing',
            'sort_order' => 10,
        ],
        [
            'name' => 'Slabs & Drainage',
            'sort_order' => 11,
        ],
        [
            'name' => 'Cornices (Conice)',
            'sort_order' => 12,
        ],
        [
            'name' => 'Wall Panels & Columns',
            'sort_order' => 13,
        ],
        [
            'name' => 'Steel Moulds',
            'sort_order' => 14,
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting category import...');
        
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($this->categories as $categoryData) {
            try {
                // Check if category exists
                $existing = ProductCategory::where('name', $categoryData['name'])->first();
                
                if ($existing) {
                    // Update if needed
                    $existing->update([
                        'sort_order' => $categoryData['sort_order'] ?? $existing->sort_order,
                        'is_active' => true,
                    ]);
                    $updated++;
                    $this->command->line("Updated: {$categoryData['name']}");
                } else {
                    // Create new category
                    ProductCategory::create([
                        'name' => $categoryData['name'],
                        'sort_order' => $categoryData['sort_order'] ?? 0,
                        'is_active' => true,
                    ]);
                    $created++;
                    $this->command->line("✅ Created: {$categoryData['name']}");
                }
                
            } catch (Exception $e) {
                $skipped++;
                $this->command->error("❌ Error: {$categoryData['name']} - " . $e->getMessage());

                Log::error("Category seeder error: " . $e->getMessage(), [
                    'category' => $categoryData['name'],
                ]);
            }
        }

        $this->command->newLine();
        $this->command->info("Results:");
        $this->command->info("✅ Created: $created");
        $this->command->info("⚒️ Updated: $updated");
        $this->command->info("⚠️ Skipped/Errors: $skipped");
        $this->command->newLine();
        $this->command->info('✅ Category import complete!');
    }

    /**
     * Get all category names (useful for validation)
     */
    public static function getCategoryNames(): array
    {
        return array_column((new self())->categories, 'name');
    }
}
