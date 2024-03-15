<?php
// Imports
use database\DB;

use function panels\deck_panel;
use function panels\tag_panel;
use function panels\account_panel;

header("Content-Type: text/html");

// Gets the search string
// If not set it will be null
// If too short it will be false
$search_string = filter_input(INPUT_GET, "search_string", FILTER_VALIDATE_REGEXP, [
	'options' => [
		'regexp' => "/^.{3,}+$/"
	]
]);

// If search string was not set or not valid give `Bad Request` response code 
if ($search_string == false || $search_string == null) {
	http_response_code(400);
	return;
}

// Establish Db connection
$db = new DB();
?>

<?php
$tag_query = $db->searchTags(
	$search_string,
	$_SESSION["account_id"] ?? null
);
if (!$tag_query->isOk()) :
?>
	<p>An error occurred trying to find tags please try again</p>
<?php elseif (!$tag_query->isEmpty()) : ?>
	<section>
		<h2>Tags</h2>

		<ul class="tag-list">
			<?php foreach ($tag_query->array() as $tag) {
				echo tag_panel($tag);
			} ?>
		</ul>
	</section>
<?php endif; ?>

<?php
$account_query = $db->searchUsers(
	$search_string,
	$_SESSION["account_id"] ?? null
);
if (!$account_query->isOk()) :
?>
	<p>An error occurred trying to find users please try again</p>
<?php
elseif (!$account_query->isEmpty()) : ?>
	<section>
		<h2>Accounts</h2>

		<ul class="user-grid">
			<?php foreach ($account_query->array() as $account) {
				echo account_panel($account);
			} ?>
		</ul>
	</section>
<?php endif; ?>

<?php
$deck_query = $db->searchDecks(
	$search_string,
	$_SESSION["account_id"] ?? null
);
if (!$deck_query->isOk()) :
?>
	<p>An error occurred trying to find users please try again</p>
<?php elseif (!$deck_query->isEmpty()) : ?>
	<section>
		<h2>Decks</h2>

		<ul class="deck-grid">
			<?php foreach ($deck_query->array() as $deck) {
				echo deck_panel($deck, $db->getDeckTopics($deck["deck_id"], $_SESSION["user_id"] ?? null));
			} ?>
		</ul>
	</section>
<?php endif; ?>

<?php if ($deck_query->isEmpty() && $account_query->isEmpty() && $tag_query->isEmpty()): ?>
	No Results were found. Please try a different term
<?php endif; ?>