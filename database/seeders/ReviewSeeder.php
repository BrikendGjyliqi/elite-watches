<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Watch;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->pluck('id')->all();
        $watches = Watch::pluck('id', 'name');

        $reviews = [
            ['watch' => 'Rolex Submariner Date 126610LN', 'rating' => 5, 'title' => 'Everything a tool watch should be', 'body' => 'I wore this daily for a year including two diving trips and it has not skipped a beat. The Cerachrom bezel still looks new despite plenty of scrapes against boat ladders. Worth every bit of the wait at the dealer.'],
            ['watch' => 'Rolex Submariner Date 126610LN', 'rating' => 4, 'title' => 'Fantastic, if a bit heavy at first', 'body' => 'Took about two weeks to get used to the weight on my wrist but now I barely notice it. The bracelet finishing is superb and the lume is genuinely bright enough to read underwater at night.'],
            ['watch' => 'Rolex GMT-Master II "Pepsi" 126710BLRO', 'rating' => 5, 'title' => 'Perfect travel companion', 'body' => 'I fly transatlantic every month for work and the second time zone hand has become essential rather than a gimmick. The Jubilee bracelet is far more comfortable than I expected from photos.'],
            ['watch' => 'Rolex Daytona 116500LN', 'rating' => 5, 'title' => 'The wait was worth it', 'body' => 'Three years on a waiting list and I still get a small thrill every time I use the pushers. The white panda dial photographs beautifully but looks even better in person under natural light.'],
            ['watch' => 'Rolex Datejust 41 126334', 'rating' => 4, 'title' => 'Elegant daily wearer', 'body' => 'This has become my go-to watch for client meetings. The blue dial shifts color subtly depending on the light, which I did not expect from the product photos. Only wish the fluted bezel felt slightly less sharp against my cuff.'],
            ['watch' => 'Omega Speedmaster Moonwatch Professional', 'rating' => 5, 'title' => 'Hand-winding is part of the charm', 'body' => 'Bought this for the history and kept wearing it for the mechanics. Winding it every morning has become a small ritual I actually look forward to. The hesalite crystal has already taken a light scuff but it polishes right out with toothpaste, just like the forums promised.'],
            ['watch' => 'Omega Seamaster Diver 300M', 'rating' => 5, 'title' => 'Incredible value at this level', 'body' => 'The wave dial catches light in a way photos never quite capture. I have taken it snorkeling twice now and the helium escape valve gives real peace of mind even though I am not a technical diver.'],
            ['watch' => 'Omega Seamaster Diver 300M', 'rating' => 4, 'title' => 'Great everyday sports watch', 'body' => 'Comfortable on the bracelet and legible from across a room thanks to the skeleton hands. Docked one star only because the clasp adjustment mechanism took some trial and error to figure out.'],
            ['watch' => 'Omega Constellation 39mm', 'rating' => 4, 'title' => 'Understated and precise', 'body' => 'Nobody at the office recognizes it, which is exactly what I wanted. Timekeeping has been within a second or two a week since I bought it, better than I expected for the price point.'],
            ['watch' => 'Patek Philippe Nautilus 5711/1A', 'rating' => 5, 'title' => 'A genuine grail', 'body' => 'Fifteen years of searching and I finally found one at a fair price through this store. The dial texture and the way the bracelet integrates into the case are simply on another level from anything else I own.'],
            ['watch' => 'Patek Philippe Aquanaut 5167A', 'rating' => 5, 'title' => 'The most comfortable strap I own', 'body' => 'The composite strap sounds like a gimmick until you actually wear it in the sun all day and it never gets sticky or smells like rubber. Understated but unmistakably special once someone looks closely.'],
            ['watch' => 'Audemars Piguet Royal Oak 15500ST', 'rating' => 5, 'title' => 'Design that still feels modern', 'body' => 'Fifty years old and the design has not aged a single day. The Grande Tapisserie dial genuinely does look slightly different from every angle, exactly as AP describes. This is the watch I always reach for on important days.'],
            ['watch' => 'Audemars Piguet Royal Oak 15500ST', 'rating' => 4, 'title' => 'Heavier than expected, in a good way', 'body' => 'The integrated bracelet gives it real substance on the wrist without feeling bulky. Service network can be slow, which is my only real complaint after two years of ownership.'],
            ['watch' => 'Cartier Santos Medium', 'rating' => 5, 'title' => 'The QuickSwitch system is genius', 'body' => 'I swap between the steel bracelet and a leather strap depending on the occasion in under thirty seconds, no tools required. The exposed screws on the bezel give it a purposeful, industrial character that photos undersell.'],
            ['watch' => 'Cartier Tank Must', 'rating' => 5, 'title' => 'Timeless does not begin to cover it', 'body' => 'This was my first proper luxury watch and I still reach for it more than pieces costing five times as much. The proportions are simply correct in a way that is hard to articulate until you try one on.'],
            ['watch' => 'TAG Heuer Carrera Chronograph 42mm', 'rating' => 4, 'title' => 'Serious watch for the money', 'body' => 'The in-house Heuer 02 movement punches well above the price bracket and the 80-hour reserve means it is often still running after a weekend off the wrist. Chronograph pushers have a satisfying, precise click.'],
            ['watch' => 'Breitling Navitimer B01 Chronograph 46', 'rating' => 4, 'title' => 'Big watch with a lot of character', 'body' => 'It wears large exactly as advertised, so try one on before buying if you have a smaller wrist. That said, the slide rule bezel is genuinely usable and not just decorative, which surprised me.'],
            ['watch' => 'IWC Portugieser Chronograph', 'rating' => 5, 'title' => 'Formal chronograph done right', 'body' => 'Most chronographs feel sporty but this one disappears comfortably under a shirt cuff at black-tie events. The railway minute track and slim numerals make it easy to read the running seconds at a glance.'],
            ['watch' => 'IWC Pilot\'s Watch Mark XX', 'rating' => 5, 'title' => 'Purposeful and legible', 'body' => 'The textile strap and soft-iron case give this real functional credibility, not just retro styling. It has quickly become the watch I grab when I want something rugged but still dressy enough for the office.'],
            ['watch' => 'Panerai Luminor Marina PAM01312', 'rating' => 4, 'title' => 'Wears bigger than the spec sheet suggests', 'body' => 'The 44mm case looks enormous in photos but sits surprisingly well thanks to the curved lugs. The sandwich dial glows for hours after a bit of sunlight, which is genuinely useful rather than a novelty.'],
        ];

        foreach ($reviews as $i => $review) {
            Review::create([
                'user_id' => $customers[$i % count($customers)],
                'watch_id' => $watches[$review['watch']],
                'rating' => $review['rating'],
                'title' => $review['title'],
                'body' => $review['body'],
                'is_approved' => true,
            ]);
        }
    }
}
