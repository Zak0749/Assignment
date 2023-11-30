<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function cards\deck_card;

$tag_id = filter_input(
	INPUT_GET,
	"tag_id",
	FILTER_VALIDATE_REGEXP,
	[
		"options" => [
			'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
		]
	]
);

// If deck_id is invalid or not set send user to error page
if ($tag_id === null || $tag_id === false) {
	http_response_code(400);
	require("errors/400.php");
	exit;
}

// Establish Db connection
$db = new DB();

$tag_query = $db->getTag($tag_id, $_SESSION["account_id"] ?? null);

// If error occurred while getting the tag send the user to an error page
if (!$tag_query->isOk()) {
	http_response_code(500);
	require("errors/500.php");
	exit;
}

// If tag doesn't exist send the user to an error page
if ($tag_query->isEmpty()) {
	http_response_code(404);
	require("errors/404.php");
	exit;
}

$tag = $tag_query->single()

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php require "components/head.php" ?>
</head>

<body>
	<?php require "components/navbar.php" ?>

	<div class="page">
		<header class="spaced-apart">
			<h1>
				<?php if (isset($_SESSION["account_id"]) && $tag["is_followed"]) : ?>
					<span class="material-symbols-outlined">
						star
					</span>
				<?php endif ?>
				<?= htmlspecialchars($tag["title"]) ?>
			</h1>
		</header>

		<main>
			<section>
				<h2>Popular</h2>

				<?php
				$popular = $db->popularByTag($tag_id, $_SESSION["account_id"] ?? null);

				if (!$popular->isOk()) : ?>
					<p>
						There was an error trying to find popular decks with this tags please try again
					</p>
				<?php elseif ($popular->isEmpty()) : ?>
					<p>
						There are no popular decks with this tag,
						when they are created they will appear here
					</p>
				<?php else : ?>
					<ul class="deck-grid">
						<?php foreach ($popular->array() as $deck) {
							echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
						} ?>
					</ul>
				<?php endif; ?>
			</section>

			<section>
				<h2>New</h2>

				<?php
				$new = $db->newByTag($tag_id, $_SESSION["account_id"] ?? null);

				if (!$new->isOk()) : ?>
					<p>
						There was an error trying to find new decks with this tags please try again
					</p>
				<?php elseif ($new->isEmpty()) : ?>
					<p>
						There are no new decks with this tag,
						when they are created they will appear here
					</p>
				<?php else : ?>
					<ul class="deck-grid">
						<?php foreach ($new->array() as $deck) {
							echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
						} ?>
					</ul>
				<?php endif; ?>
			</section>
		</main>
	</div>
</body>