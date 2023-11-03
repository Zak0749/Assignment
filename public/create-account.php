<?php

// Imports
use database\DB;
use function helpers\randomise_avatar;

// If the user is already logged in send them to an error page
if (isset($_SESSION["user_id"])) {
    http_response_code(403);
    require("errors/401.php");
    exit;
}

// Establish Db connection
$db = new DB();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>

    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <div class="page">
        <header>
            <h1>
                Create Account
            </h1>
        </header>

        <main>
            <form class="split-main" onsubmit="createAccount(this); return false;" oninput="contentChanged()">
                <section>
                    <?php $seed = randomise_avatar() ?>
                    <button name="avatar" class="avatar-input" type="button" value="<?= $seed ?>" style="background-image: url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= $seed  ?>" onclick="randomiseAvatar(this);" keyboard-shortcut="r">
                        <span class=" material-symbols-outlined">
                            change_circle
                        </span>
                    </button>

                    <div class="form-field">
                        <label for="username">Username</label>
                        <input name="username" type="username" minlength="3" maxlength="16" pattern="[\w]+" required oninput="this.setCustomValidity('')" />
                    </div>

                    <div class="form-field">
                        <label for="password">Password</label>
                        <input minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" required oninput="checkPasswordsMatch(this)" />
                    </div>

                    <div class="form-field">
                        <label for="confirm-password">Confirm Password</label>
                        <input name="confirm-password" type="password" required minlength="8" maxlength="24" pattern="[\S]+" oninput="checkPasswordsMatch(this)" />
                    </div>

                    <div class="form-field hide-large">
                        <label>Likes</label>
                        <button class="secondary-button" type="button" onclick="open_dialog('tag-select-dialog')" keyboard-shortcut="l">
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
                        <a href="login" keyboard-shortcut="l">Login</a>
                    </p>
                </section>

                <section>
                    <dialog class="cover-dialog small-only-dialog" id="tag-select-dialog">
                        <div class="spaced-apart">
                            <label>
                                <h2>Likes</h2>
                            </label>

                            <div class="icon-bar hide-large">
                                <button class="header-icon" type="button" onclick="close_dialog('tag-select-dialog')" keyboard-shortcut="e">
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
                                            <input type="checkbox" name="likes" value="<?= htmlspecialchars($tag["tag_id"]) ?>">
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
    </div>
</body>