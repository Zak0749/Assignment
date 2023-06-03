<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dist.css">
    <title>Document</title>
</head>

<body>
    <h1 class="text-3xl font-bold underline text-pink-500">
        Hello world!
    </h1>

    <h2 class="text-2xl">Current Users</h2>
    <ul>
        <?php
        $db = new SQLite3("../database/db.sqlite");

        $result = $db->query("SELECT username FROM User");

        while ($user = $result->fetchArray()) :
        ?>
            <li>
                <?= $user["username"] ?>
            </li>
        <?php endwhile ?>
    </ul>

    <h2 class="text-2xl">Become a user</h2>
    <form action="adduser.php" method="post">
        Username <input type="text" name="username" />

        <input type="submit" />
    </form>
</body>

</html>