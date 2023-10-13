<?php

// Imports
use database\DB;
use function helpers\randomise_avatar;

// If the user is already logged in send them to an error page
if (isset($_SESSION["user_id"])) {
    require "/pages/errors/forbidden.php";
    return;
}

// Establish Db connection
$db = new DB();
?>
<header>
    <h1>
        Create Account
    </h1>
</header>

<main>
    <form class="split-main" onsubmit="submit_create_account(this); return false;" oninput="contentChanged()">
        <section>
            <button name="avatar" class="avatar-input" type="button" data-seed="<?= $seed ?>" style="background-image: url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= randomise_avatar() ?>" onclick="randomise_avatar(this);">
                <span class=" material-symbols-outlined">
                    change_circle
                </span>
            </button>

            <div class="form-field">
                <label for="username">Username</label>
                <input name="username" type="username" minlength="3" maxlength="16" pattern="[\w]+" required />
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" required />
            </div>

            <div class="form-field">
                <label for="confirm-password">Confirm Password</label>
                <input name="confirm-password" type="password" required minlength="8" maxlength="24" pattern="[\S]+" oninput="check_password_match(this)" />
            </div>

            <div class="form-field hide-large">
                <label>Likes</label>
                <button class="secondary-button" type="button" onclick="open_dialog('tag-select-dialog')">
                    Show
                </button>
            </div>

            <button type="submit" value="Submit" class="primary-button" type="submit">
                <span class="material-symbols-outlined">
                    check
                </span>
                Create
            </button>

            <p class="center-text">
                Already have an account?
                <a href="/login">Login</a>
            </p>
        </section>

        <section>
            <dialog class="cover-dialog small-only-dialog" id="tag-select-dialog">
                <div class="spaced-apart">
                    <label>
                        <h2>Likes</h2>
                    </label>

                    <div class="icon-bar hide-large">
                        <button class="header-icon" type="button" onclick="close_dialog('tag-select-dialog')">
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </button>
                    </div>
                </div>

                <?php
                $tag_query = $db->getAllTags();
                if ($tag_query->isOk() && !$tag_query->isEmpty()) :
                ?>
                    <fieldset>
                        <ul class="tag-select-list">
                            <?php foreach ($tag_query->iterate() as $tag) : ?>
                                <label class="tag-select">
                                    <input type="checkbox" name="likes" value="<?= $tag["tag_id"] ?>">
                                    <span class="tag-pill-label">
                                        <?= htmlspecialchars($tag["title"]) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </ul>
                    </fieldset>
                <?php else : ?>
                    <p>An error occurred please try again</p>
                <?php endif; ?>
            </dialog>
        </section>
    </form>
</main>