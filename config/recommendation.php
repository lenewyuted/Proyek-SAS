<?php
// Data pertanyaan dan aturan untuk halaman Recommendation.
// Dipisah dari halaman supaya halaman hanya fokus menampilkan data.

$questions = [
    'purpose' => [
        'title' => 'What are the primary requirements for the PC you want to build?',
        'options' => ['gaming' => 'Gaming', 'design' => 'Design & editing', 'study' => 'Study & work'],
    ],
    'budget' => [
        'title' => 'What budget have you set aside for the purchase?',
        'options' => ['low' => '< Rp5.000.000', 'mid' => 'Rp5.000.000 - Rp10.000.000,00', 'high' => '> Rp10.000.000,00'],
    ],
    'games' => [
        'title' => 'What games or applications do you use often?',
        'options' => ['demanding' => 'Demanding games', 'light' => 'Light games', 'office' => 'Office applications'],
    ],
    'usage' => [
        'title' => 'How often do you use a PC in your daily activities?',
        'options' => ['most' => 'Most of the day', 'few' => 'A few hours a day', 'occasional' => 'Occasionally'],
    ],
];

// Rentang harga per pilihan budget [min, max]
$budgetRange = [
    'low' => [0, 5000000],
    'mid' => [5000000, 10000000],
    'high' => [10000000, PHP_INT_MAX],
];

// Kategori yang relevan untuk tiap kebutuhan (berdasarkan nama kategori di database)
$purposeCategories = [
    'gaming' => ['GPU', 'CPU', 'Motherboard', 'PSU', 'Monitor'],
    'design' => ['CPU', 'RAM', 'SSD', 'Monitor', 'GPU'],
    'study' => ['CPU', 'RAM', 'SSD', 'Monitor', 'HDD'],
];
?>
