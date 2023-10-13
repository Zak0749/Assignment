<nav class="navbar">
	<ul>
		<li>
			<a href="/">
				<span class="material-symbols-outlined">
					explore
				</span>

				Discover
			</a>
		</li>
		<!-- If signed in add library and create pages to navbar -->
		<?php if (isset($_SESSION["user_id"])) : ?>
			<li>
				<a href="/library">
					<span class="material-symbols-outlined">
						library_books
					</span>

					Library
				</a>
			</li>
			<li>
				<a href="/create_deck">
					<span class="material-symbols-outlined">
						add
					</span>

					Create
				</a>
			</li>
		<?php endif ?>
		<li>
			<a href="<?= isset($_SESSION["user_id"]) ? '/my_account' : '/not_logged_in' ?>">
				<span class="material-symbols-outlined">
					person
				</span>

				Account
			</a>
		</li>

		<li>
			<a href="/search">
				<span class="material-symbols-outlined">
					search
				</span>

				Search
			</a>
		</li>
	</ul>
</nav>