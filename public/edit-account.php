<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function helpers\randomise_avatar;

// If user is not logged in send them to an error page
if (!isset($_SESSION["user_id"])) {
	http_response_code(401);
	require("errors/401.php");
	exit;
}

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($_SESSION["user_id"]);

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
</head>

<body>
	<?php require "components/navbar.php" ?>

	<div class="page">

		<header class="spaced-apart">
			<h1>
				Edit Account
			</h1>

			<div class="icon-bar">
				<button class="header-icon" type="button" onclick="open_dialog('delete-dialog')">
					<span class="material-symbols-outlined">
						delete
					</span>
				</button>
				<a class="header-icon" type="button" href="my-account">
					<span class="material-symbols-outlined">
						close
					</span>
				</a>
			</div>
		</header>

		<main>
			<form class="split-main" onsubmit="submit_edit_account(this); return false;" oninput="contentChanged()">
				<section>
					<?php $seed = randomise_avatar() ?>
					<button name="avatar" class="avatar-input" type="button" value="<?= $user["avatar"] ?>" style="background-image: url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= $user["avatar"] ?>" onclick="randomise_avatar(this);">
						<span class=" material-symbols-outlined">
							change_circle
						</span>
					</button>

					<div class="form-field">
						<label for="username">Username</label>
						<input name="username" type="username" value="<?= $user["username"] ?>" required minlength="3" maxlength="16" pattern="[\w]+" />
					</div>

					<div class="form-field">
						<label for="password">Password</label>
						<input minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" />
					</div>

					<div class="form-field">
						<label for="confirm-password">Confirm Password</label>
						<input name="confirm-password" type="password" minlength="8" maxlength="24" pattern="[\S]+" name="password" type="password" oninput="check_password_match(this)" />
					</div>

					<div class="form-field hide-large">
						<label>Likes</label>
						<button class="secondary-button" type="button" onclick="open_dialog('tag-select-dialog')">
							Show
						</button>
					</div>

					<button type="submit" value="Submit" class="primary-button" type="submit">
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
								<h2>Likes</h2>
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
							$tags = $db->getTagsWithLikes();

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
			</form>

			<dialog class="danger-dialog" id="delete-dialog">
				<h2>Delete Account</h2>
				<p>We are sorry to see you go, hope you come back soon!</p>
				<div class="beside">
					<button class="light-danger-button" onclick="close_dialog('delete-dialog')">Cancel</button>
					<button class="danger-button" onclick="delete_account()">Delete</button>
				</div>
			</dialog>
		</main>
	</div>
</body>