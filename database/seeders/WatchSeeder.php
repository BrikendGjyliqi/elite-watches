<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WatchSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::pluck('id', 'name');
        $categories = Category::pluck('id', 'name');

        $watches = [
            [
                'name' => 'Rolex Submariner Date 126610LN',
                'brand' => 'Rolex',
                'category' => 'Diver',
                'reference_number' => '126610LN',
                'price' => 10900,
                'stock' => 6,
                'is_featured' => true,
                'is_bestseller' => true,
                'short_description' => 'The archetypal diver\'s watch, now with a sapphire cyclops and Cerachrom bezel.',
                'description' => 'The Submariner Date is the watch every dive watch is measured against. Its Oystersteel case houses the automatic Calibre 3235, delivering 70 hours of power reserve behind a unidirectional Cerachrom bezel that resists fading and scratching. Rated to 300 metres and finished with the Oyster bracelet\'s Glidelock extension system, it moves as easily from the boardroom to open water. This reference pairs a matte black dial with Chromalight hands for unmistakable low-light legibility.',
                'spec' => ['movement' => 'Automatic Cal. 3235', 'case_material' => 'Oystersteel', 'case_diameter' => '41mm', 'case_thickness' => '12.5mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '300m', 'power_reserve' => '70h', 'bracelet_material' => 'Oystersteel', 'weight' => '150g'],
            ],
            [
                'name' => 'Rolex GMT-Master II "Pepsi" 126710BLRO',
                'brand' => 'Rolex',
                'category' => 'GMT',
                'reference_number' => '126710BLRO',
                'price' => 11050,
                'stock' => 4,
                'short_description' => 'The traveler\'s icon, instantly recognizable by its red-and-blue Cerachrom bezel.',
                'description' => 'Introduced for Pan Am pilots crossing time zones, the GMT-Master II remains the definitive dual-time tool watch. The two-tone "Pepsi" Cerachrom bezel tracks a second time zone at a glance, while the Calibre 3285 movement adds a Chronergy escapement for greater efficiency and shock resistance. Its Jubilee bracelet and Oystersteel case give it a dressier presence than its diving siblings, without sacrificing any technical credibility. A genuine grail piece for collectors and frequent flyers alike.',
                'spec' => ['movement' => 'Automatic Cal. 3285', 'case_material' => 'Oystersteel', 'case_diameter' => '40mm', 'case_thickness' => '11.7mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '70h', 'bracelet_material' => 'Oystersteel Jubilee', 'weight' => '142g'],
            ],
            [
                'name' => 'Rolex Daytona 116500LN',
                'brand' => 'Rolex',
                'category' => 'Chronograph',
                'reference_number' => '116500LN',
                'price' => 15100,
                'stock' => 3,
                'is_featured' => true,
                'short_description' => 'The most coveted chronograph in the world, born on the racetrack.',
                'description' => 'Named after the Florida speedway, the Daytona was engineered for professional racing drivers who needed to measure elapsed time to the fraction of a second. The white "Panda" dial contrasts sharply against black chronograph counters and a monobloc Cerachrom tachymetric bezel. Inside, the column-wheel Calibre 4130 delivers a robust 72-hour power reserve with far fewer components than a typical chronograph movement, improving long-term reliability. Waiting lists for this reference remain years long at authorized dealers.',
                'spec' => ['movement' => 'Automatic Cal. 4130', 'case_material' => 'Oystersteel', 'case_diameter' => '40mm', 'case_thickness' => '12.4mm', 'dial_color' => 'White', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '72h', 'bracelet_material' => 'Oystersteel', 'weight' => '142g'],
            ],
            [
                'name' => 'Rolex Datejust 41 126334',
                'brand' => 'Rolex',
                'category' => 'Dress',
                'reference_number' => '126334',
                'price' => 8600,
                'stock' => 7,
                'short_description' => 'The quintessential everyday luxury watch, refined over eight decades.',
                'description' => 'First to feature an automatically changing date window, the Datejust set the template that most dress watches still follow today. This 41mm reference pairs an Oystersteel and white gold case with a fluted bezel and a rich blue sunburst dial. The Jubilee bracelet, developed specifically for this model in 1945, remains one of the most comfortable bracelets in fine watchmaking. It is the watch equally at home under a cuff at the office or on a wrist at a black-tie dinner.',
                'spec' => ['movement' => 'Automatic Cal. 3235', 'case_material' => 'Oystersteel & White Gold', 'case_diameter' => '41mm', 'case_thickness' => '11.5mm', 'dial_color' => 'Blue', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '70h', 'bracelet_material' => 'Oystersteel Jubilee', 'weight' => '155g'],
            ],
            [
                'name' => 'Omega Speedmaster Moonwatch Professional',
                'brand' => 'Omega',
                'category' => 'Chronograph',
                'reference_number' => '310.30.42.50.01.001',
                'price' => 7400,
                'stock' => 8,
                'is_featured' => true,
                'short_description' => 'The first watch worn on the Moon, unchanged in spirit since 1969.',
                'description' => 'Flight-qualified by NASA and worn during every Apollo Moon landing, the Speedmaster Professional carries more history than almost any watch in production. It retains its hand-wound Calibre 3861 movement, hesalite crystal, and tachymeter bezel exactly as astronauts knew it, right down to the caseback engraving. The manual winding ritual and mechanical chronograph pushers connect the wearer directly to its analog, purpose-built origins. Few watches offer this much verified adventure for the price.',
                'spec' => ['movement' => 'Manual Cal. 3861', 'case_material' => 'Stainless Steel', 'case_diameter' => '42mm', 'case_thickness' => '13.2mm', 'dial_color' => 'Black', 'crystal' => 'Hesalite', 'water_resistance' => '50m', 'power_reserve' => '50h', 'bracelet_material' => 'Stainless Steel', 'weight' => '134g'],
            ],
            [
                'name' => 'Omega Seamaster Diver 300M',
                'brand' => 'Omega',
                'category' => 'Diver',
                'reference_number' => '210.30.42.20.03.001',
                'price' => 5700,
                'stock' => 10,
                'is_bestseller' => true,
                'short_description' => 'James Bond\'s watch of choice, engineered to Master Chronometer standards.',
                'description' => 'The Seamaster Diver 300M combines a wave-etched dial with a helium escape valve and a ceramic bezel rated to 300 metres of water resistance. Its Co-Axial Master Chronometer movement is certified by METAS to resist magnetic fields up to 15,000 gauss, far beyond typical industry testing. The skeletonised hands and lollipop seconds hand are signature Seamaster details recognisable from a decade of film appearances. It is a genuinely tough instrument that never looks out of place off the boat.',
                'spec' => ['movement' => 'Automatic Co-Axial Master Chronometer Cal. 8800', 'case_material' => 'Stainless Steel', 'case_diameter' => '42mm', 'case_thickness' => '13.6mm', 'dial_color' => 'Summer Blue', 'crystal' => 'Sapphire', 'water_resistance' => '300m', 'power_reserve' => '55h', 'bracelet_material' => 'Stainless Steel', 'weight' => '160g'],
            ],
            [
                'name' => 'Omega Constellation 39mm',
                'brand' => 'Omega',
                'category' => 'Dress',
                'reference_number' => '131.10.39.20.02.001',
                'price' => 6800,
                'stock' => 5,
                'short_description' => 'Distinctive claws and a Roman numeral bezel define this Geneva classic.',
                'description' => 'The Constellation has worn Omega\'s "Observatory" star since 1952, awarded after the brand set chronometric precision records. This 39mm reference keeps the signature four-claw case design and fluted bezel while housing a Co-Axial Master Chronometer movement rated for exceptional accuracy and antimagnetic resistance. The silver guilloché dial catches light beautifully in daily wear. It is a refined, understated option for collectors who want Swiss precision without overt branding.',
                'spec' => ['movement' => 'Automatic Co-Axial Master Chronometer Cal. 8900', 'case_material' => 'Stainless Steel', 'case_diameter' => '39mm', 'case_thickness' => '11mm', 'dial_color' => 'Silver', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '55h', 'bracelet_material' => 'Stainless Steel', 'weight' => '135g'],
            ],
            [
                'name' => 'Patek Philippe Nautilus 5711/1A',
                'brand' => 'Patek Philippe',
                'category' => 'Sport',
                'reference_number' => '5711/1A',
                'price' => 35000,
                'stock' => 2,
                'is_featured' => true,
                'short_description' => 'The porthole-inspired sports watch that redefined the entire category.',
                'description' => 'Designed by Gerald Genta in 1976, the Nautilus turned steel into the most desirable case material in watchmaking. Its horizontally embossed blue dial and integrated bracelet remain instantly recognisable, and demand has far outstripped supply since the reference debuted. The slim automatic movement keeps the case profile remarkably thin for a watch with this much presence on the wrist. Owning one has become as much a statement of access as of taste.',
                'spec' => ['movement' => 'Automatic Cal. 26-330', 'case_material' => 'Stainless Steel', 'case_diameter' => '40mm', 'case_thickness' => '8.3mm', 'dial_color' => 'Blue', 'crystal' => 'Sapphire', 'water_resistance' => '120m', 'power_reserve' => '45h', 'bracelet_material' => 'Stainless Steel', 'weight' => '185g'],
            ],
            [
                'name' => 'Patek Philippe Aquanaut 5167A',
                'brand' => 'Patek Philippe',
                'category' => 'Sport',
                'reference_number' => '5167A',
                'price' => 22000,
                'stock' => 3,
                'short_description' => 'A younger, sportier take on the Nautilus theme with a tropical composite strap.',
                'description' => 'The Aquanaut brought the Nautilus design language to a more casual, contemporary audience with a rounded octagonal bezel and an embossed black dial. Its tropical composite strap is textured to resist wear from sun, salt, and everyday use far better than leather. Despite the sportier positioning, it retains full Patek Philippe manufacture standards inside, including the self-winding Calibre 324 S C. It has become one of the brand\'s most requested references among younger collectors.',
                'spec' => ['movement' => 'Automatic Cal. 324 S C', 'case_material' => 'Stainless Steel', 'case_diameter' => '40.8mm', 'case_thickness' => '8.25mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '120m', 'power_reserve' => '45h', 'bracelet_material' => 'Composite Rubber', 'weight' => '145g'],
            ],
            [
                'name' => 'Audemars Piguet Royal Oak 15500ST',
                'brand' => 'Audemars Piguet',
                'category' => 'Sport',
                'reference_number' => '15500ST.OO.1220ST.01',
                'price' => 25300,
                'stock' => 3,
                'is_featured' => true,
                'short_description' => 'The octagonal bezel and Tapisserie dial that invented the luxury sports watch.',
                'description' => 'Gerald Genta\'s 1972 design shocked the industry by putting a steel sports watch at the price of a gold dress watch — and it has only grown more coveted since. The signature octagonal bezel is secured by eight hexagonal screws, and the Grande Tapisserie dial pattern is cut by hand-guided machines for a subtly irregular, hand-finished texture. The integrated bracelet tapers seamlessly from the case, a hallmark of the design that countless watches have since imitated. This reference carries forward five decades of an uninterrupted design icon.',
                'spec' => ['movement' => 'Automatic Cal. 4302', 'case_material' => 'Stainless Steel', 'case_diameter' => '41mm', 'case_thickness' => '10.4mm', 'dial_color' => 'Blue Grande Tapisserie', 'crystal' => 'Sapphire', 'water_resistance' => '50m', 'power_reserve' => '70h', 'bracelet_material' => 'Stainless Steel', 'weight' => '155g'],
            ],
            [
                'name' => 'Cartier Santos Medium',
                'brand' => 'Cartier',
                'category' => 'Dress',
                'reference_number' => 'WSSA0018',
                'price' => 7300,
                'stock' => 6,
                'is_featured' => true,
                'short_description' => 'The first purpose-built pilot\'s watch, reborn with a QuickSwitch bracelet system.',
                'description' => 'Created in 1904 for aviator Alberto Santos-Dumont, the Santos is widely credited as the first men\'s wristwatch designed for practical use rather than pocket-watch convention. The exposed screws on the bezel remain a defining visual signature over a century later. This modern reference introduces the QuickSwitch and SmartLink systems, letting the steel bracelet be swapped tool-free and resized without a jeweler. It is architectural, historically significant, and remarkably easy to live with day to day.',
                'spec' => ['movement' => 'Automatic Cal. 1847 MC', 'case_material' => 'Stainless Steel', 'case_diameter' => '35.1mm', 'case_thickness' => '8.83mm', 'dial_color' => 'Silver', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '42h', 'bracelet_material' => 'Stainless Steel (QuickSwitch)', 'weight' => '110g'],
            ],
            [
                'name' => 'Cartier Tank Must',
                'brand' => 'Cartier',
                'category' => 'Dress',
                'reference_number' => 'WSTA0053',
                'price' => 3050,
                'discount_price' => 2890,
                'stock' => 12,
                'is_bestseller' => true,
                'short_description' => 'The rectangular case inspired by WWI tank treads, in its purest form.',
                'description' => 'Louis Cartier designed the Tank in 1917, taking its clean rectangular lines from the aerial view of Renault tanks on the Western Front. The Tank Must revives the model\'s more accessible quartz roots with a wide range of dial colours and a supple leather strap. Its brancards — the vertical case sides — extend the silhouette elegantly along the wrist, a proportion copied by countless dress watches since. It remains one of the most instantly recognisable case shapes in all of watchmaking.',
                'spec' => ['movement' => 'Quartz', 'case_material' => 'Stainless Steel', 'case_diameter' => '33.7mm x 25.5mm', 'case_thickness' => '6.6mm', 'dial_color' => 'Silver', 'crystal' => 'Sapphire', 'water_resistance' => '30m', 'power_reserve' => 'N/A (Quartz)', 'bracelet_material' => 'Leather', 'weight' => '45g'],
            ],
            [
                'name' => 'TAG Heuer Carrera Chronograph 42mm',
                'brand' => 'TAG Heuer',
                'category' => 'Chronograph',
                'reference_number' => 'CBS2212.FC6535',
                'price' => 4300,
                'discount_price' => 3990,
                'stock' => 9,
                'is_bestseller' => true,
                'short_description' => 'Named after the Carrera Panamericana road race, built for speed and legibility.',
                'description' => 'The Carrera was designed in the 1960s specifically to be readable at speed, with a clean dial free of unnecessary clutter despite its chronograph function. This 42mm reference runs on the in-house Calibre Heuer 02, an automatic column-wheel chronograph movement with an impressive 80-hour power reserve. A sapphire caseback shows off the oscillating weight finished with the brand\'s Geneva stripes. It offers genuine manufacture engineering at a price point well below the Swiss watchmaking elite.',
                'spec' => ['movement' => 'Automatic Calibre Heuer 02', 'case_material' => 'Stainless Steel', 'case_diameter' => '42mm', 'case_thickness' => '14.5mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '80h', 'bracelet_material' => 'Stainless Steel', 'weight' => '165g'],
            ],
            [
                'name' => 'Breitling Navitimer B01 Chronograph 46',
                'brand' => 'Breitling',
                'category' => 'Pilot',
                'reference_number' => 'AB0137211B1A1',
                'price' => 9600,
                'stock' => 5,
                'short_description' => 'The circular slide rule chronograph trusted by pilots since 1952.',
                'description' => 'The Navitimer\'s rotating slide-rule bezel was designed for pilots to calculate fuel consumption, rate of climb, and airspeed — functions still fully operational today. Its dense, busy dial has become one of the most recognisable in aviation horology, a badge of the AOPA-endorsed original. Inside sits the manufacture Breitling 01 movement, chronometer-certified by COSC for accuracy. At 46mm it wears large and confidently, exactly as a cockpit instrument should.',
                'spec' => ['movement' => 'Automatic Cal. B01', 'case_material' => 'Stainless Steel', 'case_diameter' => '46mm', 'case_thickness' => '15.1mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '30m', 'power_reserve' => '70h', 'bracelet_material' => 'Stainless Steel', 'weight' => '175g'],
            ],
            [
                'name' => 'IWC Portugieser Chronograph',
                'brand' => 'IWC Schaffhausen',
                'category' => 'Chronograph',
                'reference_number' => 'IW371614',
                'price' => 9000,
                'stock' => 4,
                'short_description' => 'A marine chronometer movement in an elegant, oversized dress case.',
                'description' => 'Commissioned in the 1930s by two Portuguese businessmen who wanted pocket-watch precision on the wrist, the Portugieser has grown into IWC\'s signature dress line. The clean two-register chronograph dial, railway minute track, and slender Arabic numerals keep the face remarkably legible for its size. Its automatic movement is derived from a proven ETA base, chronometer-adjacent in its reliability and ease of service. The alligator leather strap and polished lugs complete a genuinely formal chronograph.',
                'spec' => ['movement' => 'Automatic Cal. 69355', 'case_material' => 'Stainless Steel', 'case_diameter' => '41mm', 'case_thickness' => '12.7mm', 'dial_color' => 'Silver-Plated', 'crystal' => 'Sapphire', 'water_resistance' => '30m', 'power_reserve' => '46h', 'bracelet_material' => 'Alligator Leather', 'weight' => '98g'],
            ],
            [
                'name' => 'IWC Pilot\'s Watch Mark XX',
                'brand' => 'IWC Schaffhausen',
                'category' => 'Pilot',
                'reference_number' => 'IW328201',
                'discount_price' => 5200,
                'price' => 5500,
                'stock' => 7,
                'is_new' => true,
                'short_description' => 'The latest chapter of IWC\'s legendary Mark pilot\'s watch lineage.',
                'description' => 'The Mark series traces back to World War II-era navigation watches built to strict military specification for legibility and antimagnetic performance. The Mark XX keeps the black dial, oversized crown, and central seconds hand that define the lineage while updating the movement to the in-house calibre 32111 with a soft-iron inner case for magnetic shielding. Its textile strap is a nod to parachute webbing used by aircrews. It is a purposeful, no-nonsense pilot\'s watch with genuine wartime lineage.',
                'spec' => ['movement' => 'Automatic Cal. 32111', 'case_material' => 'Stainless Steel', 'case_diameter' => '40mm', 'case_thickness' => '11mm', 'dial_color' => 'Black', 'crystal' => 'Sapphire', 'water_resistance' => '60m', 'power_reserve' => '72h', 'bracelet_material' => 'Textile Strap', 'weight' => '95g'],
            ],
            [
                'name' => 'Hublot Big Bang Unico 42mm',
                'brand' => 'Hublot',
                'category' => 'Sport',
                'reference_number' => '441.CX.1170.RX',
                'price' => 22000,
                'stock' => 3,
                'is_new' => true,
                'short_description' => 'A skeletonised manufacture chronograph in a bold, contemporary ceramic case.',
                'description' => 'Hublot built its identity on the "Art of Fusion," pairing unconventional case materials like black ceramic with rubber and steel in a single watch. The Big Bang Unico exposes its in-house UNICO chronograph movement through an open-worked dial, letting the column wheel and twin barrels take centre stage. Its case shape is unmistakably modern, all sharp angles and exposed H-shaped screws. This is a watch for collectors who want maximum visual and mechanical drama on the wrist.',
                'spec' => ['movement' => 'Automatic Manufacture UNICO Cal. HUB1280', 'case_material' => 'Black Ceramic', 'case_diameter' => '42mm', 'case_thickness' => '14.36mm', 'dial_color' => 'Black Skeleton', 'crystal' => 'Sapphire', 'water_resistance' => '100m', 'power_reserve' => '72h', 'bracelet_material' => 'Rubber', 'weight' => '140g'],
            ],
            [
                'name' => 'Panerai Luminor Marina PAM01312',
                'brand' => 'Panerai',
                'category' => 'Diver',
                'reference_number' => 'PAM01312',
                'price' => 8000,
                'discount_price' => 7600,
                'stock' => 5,
                'is_new' => true,
                'short_description' => 'The oversized cushion case born from Italian combat diver equipment.',
                'description' => 'Panerai originally supplied the Royal Italian Navy\'s combat frogmen with instruments designed to be read in zero visibility, and the Luminor\'s crown-protecting bridge and cushion case trace directly back to that history. This Marina reference keeps the sandwich dial construction, where a layer of luminous material sits beneath a cut-out upper dial for exceptional glow. Its automatic movement and 300m water resistance make it a genuinely capable dive tool, not just a design exercise. Few watches wear their military DNA as literally as this one.',
                'spec' => ['movement' => 'Automatic Cal. P.900', 'case_material' => 'Stainless Steel', 'case_diameter' => '44mm', 'case_thickness' => '13.75mm', 'dial_color' => 'Blue Sun-Brushed', 'crystal' => 'Sapphire', 'water_resistance' => '300m', 'power_reserve' => '72h', 'bracelet_material' => 'Leather', 'weight' => '168g'],
            ],
        ];

        foreach ($watches as $data) {
            $slug = Str::slug($data['name']);

            $watch = Watch::create([
                'name' => $data['name'],
                'slug' => $slug,
                'brand_id' => $brands[$data['brand']],
                'category_id' => $categories[$data['category']],
                'reference_number' => $data['reference_number'],
                'price' => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'stock' => $data['stock'],
                'description' => $data['description'],
                'short_description' => $data['short_description'],
                'is_featured' => $data['is_featured'] ?? false,
                'is_new' => $data['is_new'] ?? false,
                'is_bestseller' => $data['is_bestseller'] ?? false,
            ]);

            $watch->spec()->create($data['spec']);

            $label = urlencode($data['name']);

            foreach ([1, 2, 3] as $position) {
                $watch->images()->create([
                    'path' => "https://placehold.co/800x800?text={$label}+{$position}",
                    'is_primary' => $position === 1,
                    'sort_order' => $position,
                ]);
            }
        }
    }
}
