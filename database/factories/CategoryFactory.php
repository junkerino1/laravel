<?php
namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $categories = [
            'Beverages', 'Snacks', 'Personal Care', 'Household',
            'Dairy', 'Bakery', 'Frozen Foods', 'Packaged Foods',
            'Fruits & Vegetables', 'Health & Wellness'
        ];

        return [
            'category_name' => $this->faker->unique()->randomElement($categories),
        ];
    }
}
