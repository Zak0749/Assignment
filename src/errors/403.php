<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <div class="page">
        <header>
            <h1>Forbidden</h1>
        </header>
        <main class="center-main">
            <section>
                <h2>403</h2>
                <p>Your account is not able to preform this action, sign out <a keyboard-shortcut="enter" href="account?account-id=<?= htmlspecialchars($_SESSION["account_id"]) ?>">here</a> ad sign in to the correct account and try again</p>
            </section>
        </main>
    </div>
</body>