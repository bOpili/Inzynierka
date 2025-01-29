<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Game::create(['title' => 'Digital Combat Simulator: World','type' => 'military']);
        Game::create(['title' => 'Il-2 Sturmovik: Great Battles','type' => 'military']);
        Game::create(['title' => 'Il-2 Sturmovik: 1946','type' => 'military']);
        Game::create(['title' => 'Microsoft Flight Simulator 2024','type' => 'civilian']);
        Game::create(['title' => 'Microsoft Flight Simulator 2020','type' => 'civilian']);
        Game::create(['title' => 'X-Plane 12','type' => 'civilian']);
        Game::create(['title' => 'X-Plane 11','type' => 'civilian']);

        Tag::create(['name' => 'PvP']);
        Tag::create(['name' => 'PvE']);
        Tag::create(['name' => 'PvPvE']);
        Tag::create(['name' => 'Trening']);
        Tag::create(['name' => 'Operacja']);
        Tag::create(['name' => 'Eskadra']);
        Tag::create(['name' => 'Noob friendly']);
        Tag::create(['name' => 'Advanced']);
        Tag::create(['name' => 'Ace']);
    }
}
