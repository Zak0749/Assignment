<?php include "../src/components/navbar.php"; ?>
<div class="page">

    <header>
        <h1>Search</h1>

        <form id="searchform" onsubmit="return false">
            <span class="material-symbols-outlined large" onchange="showResults()">search</span>
            <input id="searchbar" onchange="search(this)" type="search" class="normal-input" />
        </form>
    </header>

    <main>
        <section id="results">

        </section>
    </main>
</div>