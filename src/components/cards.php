<?php

namespace cards;

function deck_card($deck)
{ ?>
    <li class="deck-card">
        <a href="deck?deck_id=<?= $deck["deck_id"] ?>">
            <header>
                <h3>
                    <?= $deck["title"] ?>
                </h3>
                <p><?= $deck["username"] ?></p>
            </header>
            <footer>
                <div><?= $deck["plays"] ?> plays</div>
                <?php if ($_SESSION["user_id"] ?? null) : ?>
                    <?php if ($deck["saved"]) : ?>
                        <span class="material-symbols-outlined">
                            bookmark_added
                        </span>
                    <?php else : ?>
                        <span class="material-symbols-outlined">
                            bookmark
                        </span>
                    <?php endif ?>
                <?php endif ?>
            </footer>
        </a>
    </li>
<?php }

function tag_card($tag)
{ ?>
    <li class="tag-card">
        <a href="tag?tag_id=<?= $tag['tag_id'] ?>">
            <?= $tag['title'] ?>
        </a>
    </li>
<?php }



function user_card($user)
{ ?>
    <li class="user-card">
        <a href="user?user_id=<?= $user["user_id"] ?>">
            <h3>
                <?= $user['username'] ?>
            </h3>
            <p><?= $user['deck_num'] ?> <?= $user['deck_num'] == 1 ? "deck" : "decks" ?></p>

        </a>
    </li>
<?php } ?>