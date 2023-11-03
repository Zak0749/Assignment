<nav class="navbar">
	<ul>
		<li>
			<a href="index" keyboard-shortcut="alt+d">
				<span class="material-symbols-outlined">
					explore
				</span>

				Discover
			</a>
		</li>
		<!-- If signed in add library and create pages to navbar -->
		<?php if (isset($_SESSION["user_id"])) : ?>
			<li>
				<a href="library" keyboard-shortcut="alt+l">
					<span class="material-symbols-outlined">
						library_books
					</span>

					Library
				</a>
			</li>
			<li>
				<a href="create-deck" keyboard-shortcut="alt+c">
					<span class="material-symbols-outlined">
						add
					</span>

					Create
				</a>
			</li>
		<?php endif ?>
		<li>
			<a href="<?= isset($_SESSION["user_id"]) ? 'my-account' : 'not-logged-in' ?>">
				<span class="material-symbols-outlined" keyboard-shortcut="alt+a">
					person
				</span>

				Account
			</a>
		</li>

		<li>
			<a href="search" keyboard-shortcut="alt+s">
				<span class="material-symbols-outlined">
					search
				</span>

				Search
			</a>
		</li>
	</ul>
</nav>