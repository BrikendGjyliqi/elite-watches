<?php

/*
|--------------------------------------------------------------------------
| Editorial details for each maison (Brands pages)
|--------------------------------------------------------------------------
|
| Keyed by brand slug. Everything here is optional — a brand without an
| entry falls back to its database fields and the defaults below. Facts are
| limited to widely documented history (founder, founding city, signature
| creations); review before publishing and extend as the catalog grows.
|
*/

return [

    'defaults' => [
        'tagline' => 'A pursuit of precision.',
        'framing' => 'A house devoted to the measure of time.',
        'accent' => '17, 100, 102', // ÉLITE teal (r, g, b)
        'hero_image' => null,
    ],

    'rolex' => [
        'city' => 'Geneva',
        'founder' => 'Hans Wilsdorf & Alfred Davis · London',
        'signature' => ['year' => '1926', 'name' => 'The Oyster', 'note' => 'A sealed, waterproof case'],
        'tagline' => 'A crown for every achievement.',
        'framing' => 'The pursuit of the perfect tool.',
        'accent' => '11, 92, 58',
        'hero_image' => 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=2000&q=90',
    ],
    'omega' => [
        'city' => 'Biel / Bienne',
        'founder' => 'Louis Brandt · La Chaux-de-Fonds',
        'signature' => ['year' => '1969', 'name' => 'The Moonwatch', 'note' => 'The Speedmaster, worn on the Moon'],
        'tagline' => 'Precision, proven beyond the Earth.',
        'framing' => 'Precision with a sense of adventure.',
        'accent' => '24, 58, 112',
    ],
    'patek-philippe' => [
        'city' => 'Geneva',
        'founder' => 'Antoine Norbert de Patek · Geneva',
        'signature' => ['year' => '1932', 'name' => 'The Calatrava', 'note' => 'The essence of the round watch'],
        'tagline' => 'You never actually own a Patek Philippe. You merely look after it for the next generation.',
        'framing' => 'The art of the heirloom.',
        'accent' => '22, 46, 86',
    ],
    'audemars-piguet' => [
        'city' => 'Le Brassus',
        'founder' => 'Jules Louis Audemars & Edward Auguste Piguet',
        'signature' => ['year' => '1972', 'name' => 'The Royal Oak', 'note' => 'Designed by Gérald Genta'],
        'tagline' => 'To break the rules, you must first master them.',
        'framing' => 'Independence, carved in steel.',
        'accent' => '112, 28, 34',
    ],
    'cartier' => [
        'city' => 'Paris',
        'founder' => 'Louis-François Cartier · Paris',
        'signature' => ['year' => '1904', 'name' => 'The Santos', 'note' => 'Made for aviator Alberto Santos-Dumont'],
        'tagline' => 'Form, before all else.',
        'framing' => 'The jeweller who shaped time.',
        'accent' => '120, 22, 30',
    ],
    'tag-heuer' => [
        'city' => 'La Chaux-de-Fonds',
        'founder' => 'Edouard Heuer · Saint-Imier',
        'signature' => ['year' => '1963', 'name' => 'The Carrera', 'note' => 'Named for the Carrera Panamericana'],
        'tagline' => 'Measured in hundredths, lived at speed.',
        'framing' => 'Time, at full throttle.',
        'accent' => '18, 82, 70',
    ],
    'breitling' => [
        'city' => 'Grenchen',
        'founder' => 'Léon Breitling · Saint-Imier',
        'signature' => ['year' => '1952', 'name' => 'The Navitimer', 'note' => 'The pilot\'s slide-rule chronograph'],
        'tagline' => 'Instruments for professionals.',
        'framing' => 'An instrument for the sky.',
        'accent' => '96, 70, 24',
    ],
    'iwc-schaffhausen' => [
        'city' => 'Schaffhausen',
        'founder' => 'Florentine Ariosto Jones · Schaffhausen',
        'signature' => ['year' => '1939', 'name' => 'The Portugieser', 'note' => 'Made for Portuguese merchants'],
        'tagline' => 'Engineered, not merely made.',
        'framing' => 'Engineering, by the Rhine.',
        'accent' => '30, 52, 90',
    ],
    'hublot' => [
        'city' => 'Nyon',
        'founder' => 'Carlo Crocco · Geneva',
        'signature' => ['year' => '1980', 'name' => 'Gold & rubber', 'note' => 'The first fusion of the two'],
        'tagline' => 'The art of fusion.',
        'framing' => 'Tradition, fused with the future.',
        'accent' => '70, 70, 76',
    ],
    'panerai' => [
        'city' => 'Florence',
        'founder' => 'Giovanni Panerai · Florence',
        'signature' => ['year' => '1936', 'name' => 'The Radiomir', 'note' => 'Built for Italian Navy divers'],
        'tagline' => 'Born of the sea, made in Florence.',
        'framing' => 'An instrument from beneath the waves.',
        'accent' => '96, 62, 30',
    ],

];
