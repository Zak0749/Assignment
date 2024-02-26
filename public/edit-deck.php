<?php
// Begins the session for authenticating the user


// Imports
use database\Db;

$deck_id = filter_input(INPUT_GET, "deck_id", FILTER_VALIDATE_REGEXP, [
    "options" => [
        'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
    ]
]);

// If deck_id is invalid or not set send user to error page
if ($deck_id === null || $deck_id === false) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// If the user is not logged in send them to an error page
if (!isset($_SESSION["account_id"])) {
    http_response_code(401);
    require("errors/401.php");
    exit;
}

$db = new Db();

$deck_query = $db->getDeck($deck_id, $_SESSION["account_id"]);

if (!$deck_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

if ($deck_query->isEmpty()) {
    http_response_code(404);
    require("errors/404.php");
    exit;
}

$deck = $deck_query->single();

// If the user is not the owner send them to an error page
if (!$deck["is_owned"]) {
    http_response_code(403);
    require("errors/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>

    <!-- Styles for form elements -->
    <link href="styles/forms.css" rel="stylesheet">
</head>

<body>
    <?php require "components/navbar.php" ?>

    <main>

        <header>
            <header class="spaced-apart">
                <h1>
                    Edit Deck
                </h1>

                <div class="icon-bar">
                    <button class="header-icon" type="button" onclick="document.getElementById('delete-dialog').showModal()" keyboard-shortcut="d">
                        <span class="material-symbols-outlined">
                            delete
                        </span>
                    </button>
                    <a class="header-icon" type="button" href="deck?deck_id=<?= htmlspecialchars($deck_id) ?>" keyboard-shortcut="esc">
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </a>
                </div>
            </header>

            <nav class="tab-bar">
                <button onclick="changeTab(this,'info-tab')" class="selected-tab-button" keyboard-shortcut="i">
                    Info
                </button>
                <button id="card-tab-button" onclick="changeTab(this,'card-tab')" keyboard-shortcut="c">
                    Cards
                </button>
            </nav>
        </header>




        <form class="tabbed-main" data-deck-id="<?= htmlspecialchars($deck_id) ?>" onsubmit="submitEditDeck(this); return false">
            <section id="info-tab" class="split-main selected-tab">
                <section>
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input name="title" type="text" minlength="3" maxlength="32" required value="<?= htmlspecialchars($deck["title"]) ?>" />
                    </div>

                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" type="text" maxlength="512" required onload="autoHeight(this)" oninput="autoHeight(this)"><?= htmlspecialchars($deck["description"]) ?></textarea>
                    </div>

                    <div class="form-field hide-large">
                        <label>Topics</label>
                        <button class="secondary-button button" type="button" onclick="document.getElementById('tag-select-dialog').showModal()" keyboard-shortcut="t">
                            Show
                        </button>
                    </div>

                    <button type="submit" value="Submit" class="primary-button button" type="submit">
                        <span class="material-symbols-outlined">
                            save
                        </span>
                        Save
                    </button>
                </section>
                <section>
                    <dialog class="cover-dialog small-only-dialog" id="tag-select-dialog">
                        <div class="spaced-apart">
                            <label>
                                <h2>Topics</h2>
                            </label>

                            <div class="icon-bar hide-large">
                                <button class="header-icon" type="button" onclick="document.getElementById('tag-select-dialog').close()" keyboard-shortcut="e">
                                    <span class="material-symbols-outlined">
                                        close
                                    </span>
                                </button>
                            </div>
                        </div>

                        <fieldset>
                            <?php
                            $tags = $db->getTopicAnnotatedTags($deck_id, $_SESSION["account_id"] ?? null);

                            if (!$tags->isOk() || $tags->isEmpty()) : ?>
                                <p>There was an error loading the tags please try again</p>
                            <?php else : ?>
                                <ul class="tag-select-list">
                                    <?php foreach ($tags->array() as $tag) : ?>
                                        <label class="tag-select">
                                            <input type="checkbox" name="topics" value="<?= htmlspecialchars($tag["tag_id"]) ?>" <?= $tag["is_topic"] ? "checked" : "" ?>>
                                            <span class="tag-pill-label"><?= htmlspecialchars($tag["title"]) ?></span>
                                        </label>

                                    <?php endforeach ?>
                                </ul>
                            <?php endif; ?>
                        </fieldset>
                    </dialog>
                </section>
            </section>

            <section id="card-tab" data-mode="edit">
                <?php
                $cards = $db->getDeckCards($deck_id);
                if (!$cards->isOk() || $cards->isEmpty()) : ?>
                    <p>There was an error loading the cards please try again </p>
                <?php else : ?>
                    <legend>Cards:
                        <!-- Pointer events stop clicking and inputing while still validating as readoly stops html validaiton -->
                        <input id="card-counter" type="number" value="<?= htmlspecialchars($cards->rowCount()) ?>" min="8" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')">
                    </legend>

                    <ul id="card-edit-list">
                        <?php foreach ($cards->array() as $card) : ?>
                            <li>
                                <fieldset class="card-fieldset" name="cards" id="<?= htmlspecialchars($card["card_id"]) ?>">
                                    <div class="card-editor form-field" oninput="matchHeights(this)" onload="matchHeights(this)">
                                        <textarea placeholder="question" name="question" class="card-question" required maxlength="128" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"><?= htmlspecialchars($card["question"]) ?></textarea>
                                        <textarea placeholder="answer" name="answer" class="card-answer" required maxlength="256" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"><?= htmlspecialchars($card["answer"]) ?></textarea>
                                    </div>
                                    <button class="card-delete-button" type="button" onclick="removeCard(this)">
                                        <span class="material-symbols-outlined">
                                            delete
                                        </span>
                                    </button>
                                </fieldset>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>

                <div class="beside" id="edit-buttons">
                    <button type="button" class="primary-button button" onclick="addCard()" keyboard-shortcut="+">
                        <span class="material-symbols-outlined">
                            add
                        </span>

                        Add Card
                    </button>
                    <button type="button" onclick="document.getElementById('card-tab').dataset.mode = 'delete'()" class="danger-button button" keyboard-shortcut="m">
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

                    <button type="button" onclick="document.getElementById('card-tab').dataset.mode = 'edit'" class="primary-button button edit-mode-button" keyboard-shortcut="m">
                        <span class="material-symbols-outlined">
                            edit
                        </span>

                        Edit Mode
                    </button>
                </div>
            </section>
        </form>

        <dialog class="danger-dialog" id="delete-dialog">
            <h2>Delete Deck</h2>
            <div class="beside">
                <button class="light-danger-button button" onclick="document.getElementById('delete-dialog').close()" keyboard-shortcut="e">Cancel</button>
                <!-- No keyboard shortcut as want users to be sure -->
                <button class="danger-button button" onclick="deleteDeck()">Delete</button>
            </div>
        </dialog>
    </main>
</body>