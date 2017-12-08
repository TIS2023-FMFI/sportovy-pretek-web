<!-- nepotrebujeme -->
<?php
$filename = "database.db";
$db = sqlite_open($filename, 0666, $error_message) or die($error_message);

$query = "CREATE TABLE test (
          cislo INT NOT NULL PRIMARY KEY,
          meno VARCHAR(20),
          datum_narodenia DATE
          );

          INSERT INTO test VALUES (1, 'Rastislav Szabó', '1987-08-31');
          INSERT INTO test VALUES (2, 'Jozef Novák', '1979-11-02');
          INSERT INTO test VALUES (3, 'Juraj Hladký', '1989-02-13');
          INSERT INTO test VALUES (4, 'Peter Nový', '1985-11-03');
          INSERT INTO test VALUES (5, 'Andrej Lacný', '1995-01-12');
          ";
sqlite_query($db, $query);
echo "Pridaných bolo ".sqlite_changes($db)." riadkov";

sqlite_close($db);
?>