<?php
// game_logic.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const INITIAL_TIME = 30;
const INITIAL_CRYSTALS = 5;
const MAX_LEVEL = 5;
const TIME_BONUS_PER_LEVEL = 20;
const CRYSTALS_INCREASE_PER_LEVEL = 2;

function initialize_game() {
    $_SESSION['game_state'] = [
        'score' => 0,
        'level' => 1,
        'crystals_found' => 0,
        'crystals_needed' => INITIAL_CRYSTALS,
        'time_left' => INITIAL_TIME,
        'game_over' => false,
        'game_won' => false,
        'last_update' => time(),
        'crystals' => generate_crystals(INITIAL_CRYSTALS)
    ];
}

function generate_crystals(int $count): array {
    $crystals = [];
    for ($i = 0; $i < $count; $i++) {
        $crystals[] = [
            'x' => rand(50, 750),
            'y' => rand(150, 400),
            'found' => false,
            'id' => $i
        ];
    }
    return $crystals;
}

function update_timer() {
    if (!isset($_SESSION['game_state']) || $_SESSION['game_state']['game_over'] || $_SESSION['game_state']['game_won']) {
        return;
    }

    $time_passed = time() - $_SESSION['game_state']['last_update'];
    $_SESSION['game_state']['time_left'] = max(0, $_SESSION['game_state']['time_left'] - $time_passed);
    $_SESSION['game_state']['last_update'] = time();

    if ($_SESSION['game_state']['time_left'] <= 0) {
        $_SESSION['game_state']['game_over'] = true;
    }
}

function collect_crystal(int $crystal_id) {
    if (!isset($_SESSION['game_state']['crystals'][$crystal_id]) || $_SESSION['game_state']['crystals'][$crystal_id]['found']) {
        return;
    }

    $_SESSION['game_state']['crystals'][$crystal_id]['found'] = true;
    $_SESSION['game_state']['crystals_found']++;
    $_SESSION['game_state']['score'] += $_SESSION['game_state']['level'] * 100;

    if ($_SESSION['game_state']['crystals_found'] >= $_SESSION['game_state']['crystals_needed']) {
        level_up();
    }
}

function level_up() {
    if ($_SESSION['game_state']['level'] >= MAX_LEVEL) {
        $_SESSION['game_state']['game_won'] = true;
        return;
    }
    
    $_SESSION['game_state']['level']++;
    $_SESSION['game_state']['crystals_found'] = 0;
    $_SESSION['game_state']['crystals_needed'] += CRYSTALS_INCREASE_PER_LEVEL;
    $_SESSION['game_state']['time_left'] += TIME_BONUS_PER_LEVEL;
    $_SESSION['game_state']['score'] += $_SESSION['game_state']['level'] * 500;
    $_SESSION['game_state']['crystals'] = generate_crystals($_SESSION['game_state']['crystals_needed']);
}

function get_game_state(): array {
    if (!isset($_SESSION['game_state'])) {
        initialize_game();
    }
    update_timer();
    return $_SESSION['game_state'];
}