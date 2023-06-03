<?php

$db = new SQLite3("../database/db.sqlite");

$statement = $db->prepare("INSERT INTO User (username) VALUES (:username);");

$statement->bindValue(":username", $_POST["username"]);

$statement->execute();

header('Location: ' . "/");

?>