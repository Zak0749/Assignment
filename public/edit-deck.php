<?php
// Begins the session for authenticating the user


// Imports
use database\Db;

// If deck_id is not set send user to error page
if (!isset($_GET["deck_id"])) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// If the user is not logged in send them to an error page
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    require("errors/401.php");
    exit;
}

$db = new Db();

$deck_query = $db->getDeck($_GET["deck_id"]);

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
if ($_SESSION["user_id"] != $deck["user_id"]) {
    http_response_code(403);
    require("errors/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <div class="page">

        <header>
            <header class="spaced-apart">
                <h1>
                    Edit Deck
                </h1>

                <div class="icon-bar">
                    <button class="header-icon" type="button" onclick="open_dialog('delete-dialog')">
                        <span class="material-symbols-outlined">
                            delete
                        </span>
                    </button>
                    <a class="header-icon" type="button" href="deck?deck_id=<?= htmlspecialchars($_GET["deck_id"]) ?>">
                        <span class="material-symbols-outlined">
                            close
                        </span>
                    </a>
                </div>
            </header>

            <nav class="tab-bar">
                <button onclick="changeTab(this,'info-tab')" class="selected-tab-button">
                    Info
                </button>
                <button id="question-tab-button" onclick="changeTab(this,'question-tab')">
                    Questions
                </button>
            </nav>
        </header>



        <main>
            <form class="tabbed-main" data-deck-id="<?= htmlspecialchars($_GET["deck_id"]) ?>" onsubmit="submitEditDeck(this); return false" oninput="contentChanged()">
                <section id="info-tab" class="split-main selected-tab">
                    <section>
                        <div class="form-field">
                            <label for="title">Title</label>
                            <input name="title" type="text" minlength="3" maxlength="32" required value="<?= htmlspecialchars($deck["title"]) ?>" />
                        </div>

                        <div class="form-field">
                            <label for="description">Description</label>
                            <textarea name="description" type="text" maxlength="512" required oninput="autoHeight(this)"><?= htmlspecialchars($deck["description"]) ?></textarea>
                        </div>

                        <div class="form-field hide-large">
                            <label>Topics</label>
                            <button class="secondary-button" type="button" onclick="open_dialog('tag-select-dialog')">
                                Show
                            </button>
                        </div>

                        <button type="submit" value="Submit" class="primary-button" type="submit">
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
                                    <button class="header-icon" type="button" onclick="close_dialog('tag-select-dialog')">
                                        <span class="material-symbols-outlined">
                                            close
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <fieldset>
                                <?php
                                $tags = $db->getAnnotatedTopics($_GET["deck_id"]);

                                if (!$tags->isOk() || $tags->isEmpty()) : ?>
                                    <p>There was an error loading the tags please try again</p>
                                <?php else : ?>
                                    <ul class="tag-select-list">
                                        <?php foreach ($tags->iterate() as $tag) : ?>
                                            <label class="tag-select">
                                                <input type="checkbox" name="likes" value="<?= $tag["tag_id"] ?>" <?= $tag["checked"] ? "checked" : "" ?>>
                                                <span class="tag-pill-label"><?= htmlspecialchars($tag["title"]) ?></span>
                                            </label>

                                        <?php endforeach ?>
                                    </ul>
                                <?php endif; ?>
                            </fieldset>
                        </dialog>
                    </section>
                </section>

                <section id="question-tab" data-mode="edit">
                    <legend>Questions:
                        <!-- Pointer events stop clicking and inputing while still validating as readoly stops html validaiton -->
                        <input id="question-counter" type="number" value="<?= $deck["questions"] ?>" min="8" oninvalid="changeTab(document.getElementById('question-tab-button'),'question-tab')">
                    </legend>

                    <?php
                    $questions = $db->getDeckQuestions($_GET["deck_id"]);
                    if (!$questions->isOk() || $questions->isEmpty()) : ?>
                        <p>There was an error loading the questions please try again </p>
                    <?php else : ?>
                        <ul id="question-list">
                            <?php foreach ($questions->iterate() as $question) : ?>
                                <li>
                                    <fieldset name="questions" class="question" id="<?= htmlspecialchars($question["question_id"]) ?>">
                                        <div class="question-pair form-field" oninput="matchHeights(this)">
                                            <textarea placeholder="Key" name="key" class="question-key" required maxlength="128" oninvalid="changeTab(document.getElementById('question-tab-button'),'question-tab')"><?= $question["key"] ?></textarea>
                                            <textarea placeholder="Value" name="value" class="question-value" required maxlength="256" oninvalid="changeTab(document.getElementById('question-tab-button'),'question-tab')"><?= $question["value"] ?></textarea>
                                        </div>
                                        <button class="question-delete-button" type="button" onclick="removeQuestion(this)">
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
                        <button type="button" class="primary-button" onclick="addQuestion()">
                            <span class="material-symbols-outlined">
                                add
                            </span>

                            Add Question
                        </button>
                        <button type="button" onclick="question_mode_delete()" class="danger-button">
                            <span class="material-symbols-outlined">
                                delete
                            </span>

                            Delete Mode
                        </button>

                    </div>
                    <div class="beside" id="delete-buttons">
                        <button type="button" onclick="undoDeletions()" class="secondary-button">
                            <span class="material-symbols-outlined">
                                undo
                            </span>

                            Undo Deletions
                        </button>

                        <button type="button" onclick="question_mode_edit()" class="primary-button edit-mode-button">
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
                    <button class="light-danger-button" onclick="close_dialog('delete-dialog')">Cancel</button>
                    <button class="danger-button" onclick="deleteDeck()">Delete</button>
                </div>
            </dialog>
        </main>
    </div>
</body>