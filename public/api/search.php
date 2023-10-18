<?php
// Imports
use database\DB;
use function cards\deck_card;
use function cards\tag_card;
use function cards\user_card;

header("Content-Type: text/html");

// Gets the search string but if not there or invalid will be null
$search_string = filter_input(INPUT_GET, "search_string", FILTER_VALIDATE_REGEXP, [
	'options' => [
		'regexp' => "/^.{3,}+$/"
	]
]);

var_export($search_string);

// If search string was not set or not valid give `Bad Request` code
if (!$search_string) {
	http_response_code(400);
	return;
}

// Establish Db connection
$db = new DB();
?>

<?php
$tag_query = $db->searchTags($search_string);
if (!$tag_query->isOk()) :
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
$user_query = $db->searchUsers($search_string);
if (!$user_query->isOk()) :
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
$deck_query = $db->searchDecks($search_string);
if (!$deck_query->isOk()) :
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