<?php

require __DIR__ . "/vendor/autoload.php";

include __DIR__ . '/src/Framework/Database.php';

use Framework\Database;
use Dotenv\Dotenv;
use App\Config\Paths;

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$db = new Database($_ENV['DB_DRIVER'], [
  'host' => $_ENV['DB_HOST'],
  'port' => $_ENV['DB_PORT'],
  'dbname' => $_ENV['DB_NAME']
], $_ENV['DB_USER'], $_ENV['DB_PASS']);


$sqlFile = file_get_contents("./database.sql");

try {
  $db->connection->query($sqlFile);
} catch (PDOException $e) {
  die('Connection failed.');
}





// DEMO OF TRANSACTIONS

// try {

//   // by starting a transaction all sql statements must succeed
//   $db->connection->beginTransaction();

//   $db->connection->query("INSERT INTO products VALUES(99, 'gloves')");

//   $search = "Hats";

//   // placeholder with :name
//   $query = "SELECT * FROM products WHERE name=:name";

//   // ALWAYS use prepared statements
//   $stmt = $db->connection->prepare($query);

//   // we can also manually bind/insert values and check types before executing, then we don't have to pass array
//   //$stmt->bindValue('name', $search, PDO::PARAM_STR);

//   // SQL validates value before inserting to make sure it will not alter query
//   $stmt->execute([
//     "name" => 'gloves'
//   ]);

//   var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));

//   $db->connection->commit();
// } catch (Exception $e) {
//   if ($db->connection->inTransaction()) {
//     $db->connection->rollBack();
//   }
//   echo "Transaction failed";
// }
