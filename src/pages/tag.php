<?php

// Imports
use database\DB;
use function cards\deck_card;

// If tag_id is not set send user to error page
if (!isset($_GET["tag_id"])) {
	require 'pages/errors/bad_request.php';
	return;
}

// Establish Db connection
$db = new DB();

$tag_query = $db->getTag($_GET["tag_id"]);

// If error occurred while getting the tag send the user to an error page
if (!$tag_query->isOk()) {
	require 'pages/errors/internal_server_error.php.php';
	return;
}

// If tag doesn't exist send the user to an error page
if ($tag_query->isEmpty()) {
	require 'pages/errors/not_found.php';
	return;
}

$tag = $tag_query->single()

?>
<header>
	<h1><?= $tag["title"] ?></h1>
</header>

<main>
	<section>
		<h2>Popular</h2>

		<?php
		$popular = $db->popularByTag($_GET["tag_id"]);

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
				<?php foreach ($popular->iterate() as $deck) {
					echo deck_card($deck, $db->getTopics($deck["deck_id"]));
				} ?>
			</ul>
		<?php endif; ?>
	</section>

	<section>
		<h2>New</h2>

		<?php
		$new = $db->newByTag($_GET["tag_id"]);

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
				<?php foreach ($new->iterate() as $deck) {
					echo deck_card($deck, $db->getTopics($deck["deck_id"]));
				} ?>
			</ul>
		<?php endif; ?>
	</section>
</main>