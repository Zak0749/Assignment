<?php

namespace cards;

use database\DbResult;

function deck_card(array $deck, DbResult $tag_query)
{
?>
	<li class="deck-card">
		<a href="deck?deck_id=<?= htmlspecialchars($deck["deck_id"]) ?>">
			<header>
				<div>
					<h3>
						<?= htmlspecialchars($deck["title"]) ?>
					</h3>
					<p><?= htmlspecialchars($deck["username"]) ?></p>
					<div class="plays"><?= htmlspecialchars($deck["plays"]) ?> plays</div>

				</div>
				<?php if (isset($_SESSION["user_id"])) : ?>
					<?php if ($deck["saved"]) : ?>
						<span class="material-symbols-outlined">
							bookmark_added
						</span>
					<?php else : ?>
						<span class="material-symbols-outlined">
							bookmark_add
						</span>
					<?php endif ?>
				<?php endif ?>
			</header>

			<?php if ($tag_query->isOk() && !$tag_query->isEmpty()) : ?>
				<ul class="deck-card-topics">
					<?= htmlspecialchars(implode("•", array_column($tag_query->array(), "title"))); ?>
				</ul>
			<?php endif; ?>
		</a>
	</li>
<?php }

function tag_card($tag)
{
?>
	<li class="tag-card">
		<a href="tag?tag_id=<?= htmlspecialchars($tag['tag_id']) ?>">
			<?= htmlspecialchars($tag['title']) ?>
		</a>
	</li>
<?php }



function user_card($user)
{
?>
	<li class="user-card">
		<a href="user?user_id=<?= htmlspecialchars($user["user_id"]) ?>">
			<img src="https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= htmlspecialchars($user["avatar"]) ?>">
			<div>
				<h3>
					<?= htmlspecialchars($user['username']) ?>
				</h3>
				<p><?= htmlspecialchars($user['deck_num']) ?> <?= $user['deck_num'] == 1 ? "deck" : "decks" ?></p>
			</div>
		</a>
	</li>
<?php } ?>