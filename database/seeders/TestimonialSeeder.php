<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectTypes = [
            'Residential Installation',
            'Commercial Wiring',
            'Emergency Repair',
            'Lighting Upgrade',
            'Panel Replacement'
        ];

        $test = [
            'author_name' => fake()->name(),
            'author_title' => fake()->optional()->jobTitle(),
            'author_company' => fake()->optional()->company(),
            'author_location' => fake()->city() . ', ' . fake()->stateAbbr(),
            'content' => fake()->paragraph(3),
            'rating' => fake()->numberBetween(4, 5),
            'project_type' => fake()->randomElement($projectTypes),
            'is_featured' => fake()->boolean(20),
            'is_approved' => true,
            'is_displayed' => true,
            'display_order' => fake()->optional()->numberBetween(1, 100),
            'project_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];

        Testimonial::create($test);
    }

    // public function featured()
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'is_featured' => true
    //     ]);
    // }

    // public function unapproved()
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'is_approved' => false
    //     ]);
    // }
}
