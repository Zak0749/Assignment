<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <main>
        <header>
            <h1 >Search</h1>

            <!-- Submit Disabled as it will update live  -->
            <form class="search-bar " onsubmit="return false" oninput="displaySearchResults(this)">
                <span class="material-symbols-outlined">search</span>
                <input name="search" type="search" />
            </form>
        </header>

        <!-- Where the results will go when loaded -->
        <div id="search-results"></div>
    </main>
</body>