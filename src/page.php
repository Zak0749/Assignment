<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/images/favicon.ico" sizes="any">
    <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Styling -->
    <link href="styles/styles.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/cards.css" rel="stylesheet">
    <link href="styles/variables.css" rel="stylesheet">
    <link href="styles/search.css" rel="stylesheet">
    <link href="styles/forms.css" rel="stylesheet">
    <link href="styles/play.css" rel="stylesheet">

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet">

    <!-- scripts -->
    <?php if ($path == "/play") : ?>
        <script src="
https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js
"></script>

    <?php endif; ?>
    <script type="text/javascript" src="scripts/scripts.js"></script>

    <title>Learnify</title>
</head>

<body>
    <?php
    // Insert the navbar
    if ($path != "/play") {
        require "components/navbar.php";
    }
    ?>

    <div class="page">
        <?php
        // Match the url to the page and insert it 
        require match ($path) {
            '/', '' => 'pages/discover.php',
            '/my_account' => 'pages/my_account.php',
            "/not_logged_in" => 'pages/not_logged_in.php',
            '/my_account' => 'pages/my_account.php',
            '/edit_account' => 'pages/edit_account.php',
            '/create_deck' => 'pages/create_deck.php',
            '/deck' => 'pages/deck.php',
            '/user' => 'pages/user.php',
            '/search' => 'pages/search.php',
            '/tag' => 'pages/tag.php',
            '/library' => 'pages/library.php',
            '/login' => 'pages/login.php',
            '/create_account' => 'pages/create_account.php',
            '/edit_deck' => 'pages/edit_deck.php',
            '/play' => 'pages/play.php',
            default => 'pages/errors/not_found.php'
        }
        ?>
    </div>
</body>

</html>