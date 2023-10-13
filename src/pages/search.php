<header>
    <h1>Search</h1>

    <!-- Submit Disabled as it will update live  -->
    <form class="search-bar" onsubmit="return false" oninput="display_search_results(this)">
        <span class="material-symbols-outlined">search</span>
        <input name="search" type="search" />
    </form>
</header>

<!-- Where the results will go when loaded -->
<main id="search-results"></main>