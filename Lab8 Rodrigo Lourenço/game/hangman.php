<?php
session_start();
header('Content-Type: application/json');

// Word list in Slovak
$words = [
    'slovensko',  // Slovakia
    'bratislava', // Capital city
    'tatry',      // Mountains
    'voda',       // Water
    'skola',      // School
    'mesto',      // City
    'auto',       // Car
    'chlieb',     // Bread
    'zima',       // Winter
    'jar',        // Spring
    'leto',       // Summer
    'jeseň',      // Autumn
    'dom',        // House
    'strom',      // Tree
    'rieka',      // River
    'hora',       // Mountain
    'vlak',       // Train
    'telefon',    // Phone
    'ucebnica',   // Textbook
    'lampa'       // Lamp
];


if (isset($_POST['restart']) || !isset($_SESSION['word'])) {
    $_SESSION['word'] = $words[array_rand($words)];
    $_SESSION['display'] = str_repeat('_', strlen($_SESSION['word']));
    $_SESSION['guessed'] = [];
    $_SESSION['attempts'] = 0;
    $_SESSION['maxAttempts'] = 6;
}

$won = $_SESSION['display'] === $_SESSION['word'];
$lost = $_SESSION['attempts'] >= $_SESSION['maxAttempts'];

if (
    isset($_POST['letter']) &&
    !$won && !$lost
) {
    $letter = strtolower($_POST['letter']);
    if (!in_array($letter, $_SESSION['guessed']) && ctype_alpha($letter) && strlen($letter) === 1) {
        $_SESSION['guessed'][] = $letter;
        if (strpos($_SESSION['word'], $letter) !== false) {
            for ($i = 0; $i < strlen($_SESSION['word']); $i++) {
                if ($_SESSION['word'][$i] === $letter) {
                    $_SESSION['display'][$i] = $letter;
                }
            }
        } else {
            $_SESSION['attempts']++;
        }
    }
}

echo json_encode([
    'display' => implode(' ', str_split($_SESSION['display'])),
    'guessed' => $_SESSION['guessed'],
    'attempts' => $_SESSION['attempts'],
    'maxAttempts' => $_SESSION['maxAttempts'],
    'won' => $_SESSION['display'] === $_SESSION['word'],
    'lost' => $_SESSION['attempts'] >= $_SESSION['maxAttempts'],
    'word' => ($_SESSION['display'] === $_SESSION['word'] || $_SESSION['attempts'] >= $_SESSION['maxAttempts']) ? $_SESSION['word'] : null
]);