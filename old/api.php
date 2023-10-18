<?php



require match ($path) {
    '/api/search_results' => 'api/search_results.php',
    '/api/create_account' => 'api/create_account.php',
    '/api/create_deck' => 'api/create_deck.php',
    '/api/edit_deck' => 'api/edit_deck.php',
    '/api/delete_deck' => 'api/delete_deck.php',
    '/api/edit_account' => 'api/edit_account.php',
    '/api/delete_account' => 'api/delete_account.php',
    '/api/save' => 'api/save.php',
    '/api/delete_save' => 'api/delete_save.php',
    '/api/login' => 'api/login.php',
    '/api/logout' => 'api/logout.php',
    '/api/save_results' => 'api/save_results.php',
    default => 'api/not_found.php',
};
