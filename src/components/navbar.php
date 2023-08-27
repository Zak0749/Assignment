<?php
?>

<nav>
    <ul>
        <li>
            <a href="/">
                <span class="material-symbols-outlined">
                    explore
                </span>

                Discover
            </a>
        </li>
        <?php if ($_SESSION["user_id"] ?? null ?? false) : ?>
            <li>
                <a href="/library">
                    <span class="material-symbols-outlined">
                        library_books
                    </span>

                    Library
                </a>
            </li>
            <li>
                <a href="/create">
                    <span class="material-symbols-outlined">
                        add
                    </span>

                    Create
                </a>
            </li>
        <?php endif ?>
        <li>
            <a href="/account">
                <span class="material-symbols-outlined">
                    person
                </span>

                Account
            </a>
        </li>

        <li>
            <a href="/search">
                <span class="material-symbols-outlined">
                    search
                </span>

                Search
            </a>
        </li>
    </ul>
</nav>