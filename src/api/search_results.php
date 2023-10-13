<?php
// Imports
use database\DB;
use function cards\deck_card;
use function cards\tag_card;
use function cards\user_card;

// Establish Db connection
$db = new DB();
?>

<?php
$tag_query = $db->searchTags($_GET["search_string"]);
if (!$tag_query->isOk()) :
	// Gives response of `Internal Server Error`
	http_response_code(500)
?>
	<p>An error occurred trying to find tags please try again</p>
<?php elseif (!$tag_query->isEmpty()) : ?>
	<section>
		<h2>Tags</h2>

		<ul class="tag-list">
			<?php foreach ($tag_query->iterate() as $tag) {
				echo tag_card($tag);
			} ?>
		</ul>
	</section>
<?php endif; ?>

<?php
$user_query = $db->searchUsers($_GET["search_string"]);
if (!$user_query->isOk()) :
	// Gives response of `Internal Server Error`
	http_response_code(500)
?>
	<p>An error occurred trying to find users please try again</p>
<?php
	echo $user_query->isEmpty();
elseif (!$user_query->isEmpty()) : ?>
	<section>
		<h2>Users</h2>

		<ul class="user-grid">
			<?php foreach ($user_query->iterate() as $user) {
				echo user_card($user);
			} ?>
		</ul>
	</section>
<?php endif; ?>

<?php
$deck_query = $db->searchDecks($_GET["search_string"]);
if (!$deck_query->isOk()) :
	// Gives response of `Internal Server Error`
	http_response_code(500)
?>
	<p>An error occurred trying to find users please try again</p>
<?php elseif (!$deck_query->isEmpty()) : ?>
	<section>
		<h2>Decks</h2>

		<ul class="deck-grid">
			<?php foreach ($deck_query->iterate() as $deck) {
				echo deck_card($deck, $db->getTopics($deck["deck_id"]));
			} ?>
		</ul>
	</section>
<?php endif; ?>