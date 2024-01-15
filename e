object(PDOException)#5 (8) { 
    ["message":protected]=> string(99) "SQLSTATE[42601]: Syntax error: 7 ERROR: syntax error at or near "FROM" LINE 6: FROM Deck ^" 
    ["string":"Exception":private]=> string(0) "" 
    ["code":protected]=> string(5) "42601" 
    ["file":protected]=> string(33) "/var/www/src/modules/database.php" 
    ["line":protected]=> int(167) 
    ["trace":"Exception":private]=> array(2) { 
        [0]=> array(6) { ["file"]=> string(33) "/var/www/src/modules/database.php" 
            ["line"]=> int(167) 
            ["function"]=> string(7) "execute" 
            ["class"]=> string(12) "PDOStatement" 
            ["type"]=> string(2) "->" ["args"]=> array(0) { } 
        } 
        [1]=> array(6) { 
            ["file"]=> string(23) "/var/www/html/index.php" 
            ["line"]=> int(53) 
            ["function"]=> string(11) "getFeatured" 
            ["class"]=> string(11) "database\Db" 
            ["type"]=> string(2) "->" ["args"]=> array(0) { } 
        } 
    } 
    ["previous":"Exception":private]=> NULL 
    ["errorInfo"]=> array(3) { [0]=> string(5) "42601" [1]=> int(7) [2]=> string(66) "ERROR: syntax error at or near "FROM" LINE 6: FROM Deck ^" } 

}