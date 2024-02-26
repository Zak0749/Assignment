<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function helpers\randomise_avatar;

// If user is not logged in send them to an error page
if (!isset($_SESSION["account_id"])) {
	http_response_code(401);
	require("errors/401.php");
	exit;
}

// Establish Db connection
$db = new DB();

$user_query = $db->getAccount($_SESSION["account_id"], $_SESSION["account_id"]);

// If error occurred while finding the user send user to error page
if (!$user_query->isOk()) {
	http_response_code(500);
	require("errors/500.php");
	exit;
}

// If user couldn't be found send the user to an error page
if ($user_query->isEmpty()) {
	http_response_code(404);
	require("errors/404.php");
	exit;
}

$user = $user_query->single();

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

		<header class="spaced-apart">
			<h1>
				Edit Account
			</h1>

			<div class="icon-bar">
				<button class="header-icon" type="button" onclick="document.getElementById('delete-dialog').showModal()" keyboard-shortcut="d">
					<span class="material-symbols-outlined">
						delete
					</span>
				</button>
				<a class="header-icon" type="button" href="account" keyboard-shortcut="esc">
					<span class="material-symbols-outlined">
						close
					</span>
				</a>
			</div>
		</header>


		<form class="split-main" onsubmit="submitEditAccount(this); return false;" >
			<section>
				<?php $seed = randomise_avatar() ?>
				<button name="avatar" class="avatar-input" type="button" value="<?= htmlspecialchars($user["avatar"]) ?>" style="background-image: url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= htmlspecialchars($user["avatar"]) ?>" onclick="randomiseAvatar(this);" keyboard-shortcut="r">
					<span class=" material-symbols-outlined">
						change_circle
					</span>
				</button>

				<div class="form-field">
					<label for="username">Username</label>
					<input name="username" type="username" value="<?= htmlspecialchars($user["username"]) ?>" required minlength="3" maxlength="16" pattern="[\w]+" oninput="this.setCustomValidity('');" />
				</div>

				<div class="form-field">
					<label for="password">Password</label>
					<input minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" oninput="checkPasswordsMatch(this)" />
				</div>

				<div class="form-field">
					<label for="confirm-password">Confirm Password</label>
					<input name="confirm-password" type="password" minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" oninput="checkPasswordsMatch(this)" />
				</div>

				<div class="form-field hide-large">
					<label>Follows</label>
					<button class="secondary-button button" type="button" onclick="document.getElementById('tag-select-dialog').showModal()" keyboard-shortcut="l">
						Show
					</button>
				</div>

				<button type="submit" value="Submit" class="primary-button button" type="submit">
					<span class="material-symbols-outlined">
						check
					</span>
					Save
				</button>
			</section>

			<section>
				<dialog class="cover-dialog small-only-dialog" id="tag-select-dialog">
					<div class="spaced-apart">
						<label>
							<h2>Follows</h2>
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
						$tags = $db->getAllTags($_SESSION["account_id"]);

						if (!$tags->isOk() || $tags->isEmpty()) : ?>
							<p>There was an error loading the tags please try again</p>
						<?php else : ?>
							<ul class="tag-select-list">
								<?php foreach ($tags->array() as $tag) : ?>
									<label class="tag-select">
										<input type="checkbox" name="follows" value="<?= htmlspecialchars($tag["tag_id"]) ?>" <?= $tag["is_followed"] ? "checked" : "" ?>>
										<span class="tag-pill-label"><?= htmlspecialchars($tag["title"]) ?></span>
									</label>
								<?php endforeach ?>
							</ul>
						<?php endif; ?>
					</fieldset>
				</dialog>
			</section>
		</form>

		<dialog class="danger-dialog" id="delete-dialog">
			<h2>Delete Account</h2>
			<p>We are sorry to see you go, hope you come back soon!</p>
			<div class="beside">
				<button class="light-danger-button button" onclick="document.getElementById('delete-dialog').close()" keyboard-shortcut="e">Cancel</button>
				<!-- No questionboard shortcut as want users to be sure -->
				<button class="danger-button button" onclick="deleteAccount()">Delete</button>
			</div>
		</dialog>
	</main>
	</div>
</body>