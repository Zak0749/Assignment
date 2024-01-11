<?php
// Imports
use database\Db;

// If the user is not logged in send them to an error page
if (!isset($_SESSION["account_id"])) {
    http_response_code(401);
    require("errors/401.php");
    exit;
}

// Establish database connection
$db = new Db();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <main>

        <header>
            <h1>Create</h1>

            <nav class="tab-bar">
                <button onclick="changeTab(this,'info-tab')" class="selected-tab-button" keyboard-shortcut="i">
                    Info
                </button>
                <button id="card-tab-button" onclick="changeTab(this,'card-tab')" keyboard-shortcut="q">
                    Cards
                </button>
            </nav>
        </header>


        <form class="tabbed-main" onsubmit="createDeck(this); return false" oninput="contentChanged()">
            <section id="info-tab" class="split-main selected-tab">
                <section>
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input name="title" type="text" minlength="3" maxlength="32" required />
                    </div>

                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" type="text" minlength="3" maxlength="256" required oninput="autoHeight(this)"></textarea>
                    </div>

                    <div class="form-field hide-large">
                        <label>Topics</label>
                        <button class="secondary-button" type="button" onclick="open_dialog('tag-select-dialog')" keyboard-shortcut="t">
                            Show
                        </button>
                    </div>

                    <button type="submit" value="Submit" class="primary-button button" type="submit">
                        <span class="material-symbols-outlined">
                            check
                        </span>
                        Create
                    </button>
                </section>
                <section>
                    <dialog class="cover-dialog small-only-dialog" id="tag-select-dialog">
                        <div class="spaced-apart">
                            <label>
                                <h2>Topics</h2>
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
                        $tag_query = $db->getAllTags($_SESSION["account_id"]);
                        if ($tag_query->isOk() && !$tag_query->isEmpty()) :
                        ?>
                            <fieldset>
                                <ul class="tag-select-list">
                                    <?php foreach ($tag_query->array() as $tag) : ?>
                                        <label class="tag-select">
                                            <input type="checkbox" name="topics" value="<?= htmlspecialchars($tag["tag_id"]) ?>">
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
            </section>
            <section id="card-tab" data-mode="edit">
                <legend>Cards:
                    <!-- Pointer events stop clicking and inputing while still validating as readoly stops html validaiton -->
                    <input id="card-counter" type="number" value="1" min="8" oninvalid="changeTab(document.getElementById('card-tab-button'), 'card-tab')">
                </legend>

                <ul id="card-list">
                    <li>
                        <fieldset name="cards" class="card">
                            <div class="card-pair form-field" oninput="matchHeights(this)">
                                <textarea placeholder="question" name="question" class="card-question" required maxlength="128" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"></textarea>
                                <textarea placeholder="answer" name="answer" class="card-answer" required maxlength="256" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"></textarea>
                            </div>
                            <button class="card-delete-button" type="button" onclick="removeCard(this)">
                                <span class="material-symbols-outlined">
                                    delete
                                </span>
                            </button>
                        </fieldset>
                    </li>
                </ul>
                <div class="beside" id="edit-buttons">
                    <button type="button" class="primary-button button" onclick="addCard()" keyboard-shortcut="+">
                        <span class="material-symbols-outlined">
                            add
                        </span>

                        Add Card
                    </button>
                    <button type="button" onclick="cardModeDelete()" class="danger-button button" keyboard-shortcut="m">
                        <span class="material-symbols-outlined">
                            delete
                        </span>

                        Delete Mode
                    </button>

                </div>
                <div class="beside" id="delete-buttons">
                    <button type="button" onclick="undoDeletions()" class="secondary-button button" keyboard-shortcut="u">
                        <span class="material-symbols-outlined">
                            undo
                        </span>

                        Undo Deletions
                    </button>

                    <button type="button" onclick="cardModeEdit()" class="primary-button edit-mode-button button" keyboard-shortcut="m">
                        <span class="material-symbols-outlined">
                            edit
                        </span>

                        Edit Mode
                    </button>
                </div>
            </section>
        </form>
    </main>
</body>