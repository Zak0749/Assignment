<!DOCTYPE html>
<html lang="en">

<head>

    <!-- scripts -->
    <?php if ($path == "/play") : ?>
        <script src="
https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js
"></script>

    <?php endif; ?>
    
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