<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Jobs\ProcessTicketImage;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'promoter',
            'email' => 'promoter@gmail.com',
            'password' => bcrypt('password')
        ]);

        $images = [];

        Ticket::factory()->for($user)->published()->create([
            'title' => 'Spring Training',
            'subtitle' => 'Gentile Center',
            'price' => 2500,
            'quantity' => 100,
            'image' => $images[] = Storage::disk('public')->putFile('ticket-posters', new File('public/images/image1.png')),
            'created_at' => now(),
        ]);

        Ticket::factory()->for($user)->published()->create([
            'title' => 'T20 South Africa vs Australia',
            'subtitle' => 'SuperSport Park Gauteng - South Africa',
            'price' => 3000,
            'quantity' => 100,
            'image' => $images[] = Storage::disk('public')->putFile('ticket-posters', new File('public/images/image3.png')),
            'created_at' => now()->addMinute(1),
        ]);

        Ticket::factory()->for($user)->published()->create([
            'title' => 'NFL Pro Bowl Games',
            'subtitle' => 'Camping World Stadium',
            'price' => 2000,
            'quantity' => 100,
            'image' => $images[] = Storage::disk('public')->putFile('ticket-posters', new File('public/images/image2.png')),
            'created_at' => now()->addMinute(2),
        ]);

        $ticket = Ticket::factory()->for($user)->published()->create([
            'title' => 'T20 South Africa vs Australia',
            'subtitle' => 'SuperSport Park Gauteng - South Africa',
            'price' => 1900,
            'quantity' => 100,
            'image' => $images[] = Storage::disk('public')->putFile('ticket-posters', new File('public/images/image4.png')),
            'created_at' => now()->addMinute(3),
        ]);

        foreach ($images as $image) {
            ProcessTicketImage::dispatch($image);
        }

        $ticket->update(['quantity' => $ticket->quantity - 4, 'sold_count' => 4]);

        $order = Order::create([
            'email' => 'somemail@gmail.com',
            'quantity' => 4,
            'charged' => 4 * $ticket->price,
            'last_4' => '1234',
        ]);

        $order->addTicket($ticket, 4);
    }
}
