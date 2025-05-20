<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessCategories = [
            [
                'id' => 1,
                'name' => 'Entertainment',
                'description' => 'Businesses that provide entertainment services such as movies, concerts, and performances.'
            ],
            [
                'id' => 2,
                'name' => 'Food & Beverage',
                'description' => 'Restaurants, cafes, and food services offering meals and beverages.'
            ],
            [
                'id' => 3,
                'name' => 'Retail',
                'description' => 'Stores and shops selling consumer goods directly to customers.'
            ],
            [
                'id' => 4,
                'name' => 'Health & Wellness',
                'description' => 'Businesses focused on health services, fitness, and wellness products.'
            ],
            [
                'id' => 5,
                'name' => 'Technology',
                'description' => 'Companies involved in the development and distribution of technology products and services.'
            ],
            [
                'id' => 6,
                'name' => 'Education',
                'description' => 'Institutions and services related to education and training.'
            ],
            [
                'id' => 7,
                'name' => 'Finance',
                'description' => 'Financial services including banks, insurance, and investment firms.'
            ],
            [
                'id' => 8,
                'name' => 'Travel & Tourism',
                'description' => 'Services related to travel, accommodations, and tourism.'
            ],
            [
                'id' => 9,
                'name' => 'Real Estate',
                'description' => 'Businesses involved in buying, selling, and renting properties.'
            ],
            [
                'id' => 10,
                'name' => 'Construction',
                'description' => 'Companies that provide construction services for buildings and infrastructure.'
            ],
            [
                'id' => 11,
                'name' => 'Transportation & Logistics',
                'description' => 'Services that facilitate the movement of goods and people, including shipping and freight services.'
            ],
            [
                'id' => 12,
                'name' => 'Marketing & Advertising',
                'description' => 'Agencies and services focused on promoting products and services through various channels.'
            ],
            [
                'id' => 13,
                'name' => 'Automotive',
                'description' => 'Businesses involved in the sale, repair, and maintenance of vehicles.'
            ],
            [
                'id' => 14,
                'name' => 'Arts & Crafts',
                'description' => 'Businesses focused on the creation and sale of artistic and handcrafted products.'
            ],
            [
                'id' => 15,
                'name' => 'Pet Services',
                'description' => 'Businesses providing services and products for pets, including grooming, boarding, and training.'
            ],
            [
                'id' => 16,
                'name' => 'Personal Care',
                'description' => 'Services related to beauty, grooming, and personal care products.'
            ],
            [
                'id' => 17,
                'name' => 'Consulting',
                'description' => 'Firms providing expert advice and services in various fields such as business, finance, and IT.'
            ],
            [
                'id' => 18,
                'name' => 'Home Services',
                'description' => 'Businesses providing services for home maintenance, repair, and improvement.'
            ],
            [
                'id' => 19,
                'name' => 'Media & Publishing',
                'description' => 'Companies involved in the production and distribution of media content, including books, magazines, and online publications.'
            ],
            [
                'id' => 20,
                'name' => 'Event Planning',
                'description' => 'Services focused on planning and organizing events such as weddings, corporate functions, and parties.'
            ]
        ];

        DB::table('business_categories')->insert($businessCategories);
    }
}
