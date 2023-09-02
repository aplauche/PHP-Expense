<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
  public function __construct(private Database $db)
  {
  }

  public function create(array $formData)
  {

    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "INSERT INTO transactions(user_id, description, amount, date)
      VALUES(:user_id, :description, :amount, :date)",
      [
        'user_id' => $_SESSION['user'],
        'description' => $formData['description'],
        'amount' => $formData['amount'],
        'date' => $formattedDate
      ]
    );
  }

  public function getUserTransaction(string $id)
  {
    return $this->db->query(
      "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date FROM transactions WHERE id = :id AND user_id = :user_id",
      ["id" => $id, 'user_id' => $_SESSION['user']]
    )->find();
  }

  public function getUserTransactions(int $length, int $offset)
  {

    $searchTerm = $_GET['s'] ?? '';

    // escape percent and unerscore to prevent errors with the LIKE clause for search
    $searchTerm = addcslashes($searchTerm, '%_');

    // fetch transactions that match search term AND belong to user
    $params = ["user_id" => $_SESSION['user'], "description" => "%{$searchTerm}%"];

    $transactions = $this->db->query(
      "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date 
      FROM transactions WHERE user_id = :user_id 
      AND description LIKE :description
      LIMIT {$length} OFFSET {$offset}",
      $params
    )->findAll();


    // Iterate over transaction results and query for receipts related to each
    $transactions = array_map(function (array $transaction) {
      $transaction['receipts'] = $this->db->query(
        "SELECT * FROM receipts WHERE transaction_id=:transaction_id",
        ["transaction_id" => $transaction['id']]
      )->findAll();

      return $transaction;
    }, $transactions);

    // Run a seperate query to get our count for pagination logic
    $transactionCount = $this->db->query(
      "SELECT COUNT(*)
      FROM transactions WHERE user_id = :user_id 
      AND description LIKE :description",
      $params
    )->count();

    return [
      "transactions" => $transactions,
      "transactionCount" => $transactionCount
    ];
  }

  public function update(array $formData, int $id)
  {

    // Mysql expects a datetime object, but we just have date
    $formattedDate = "{$formData['date']} 00:00:00";

    $this->db->query(
      "UPDATE transactions
      SET description = :description, amount = :amount, date = :date
      WHERE id = :id AND user_id = :user_id",
      [
        "description" => $formData['description'],
        "amount" => $formData['amount'],
        "date" => $formattedDate,
        "id" => $id,
        "user_id" => $_SESSION['user'],
      ]
    );
  }

  public function delete(int $id)
  {
    $this->db->query(
      "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
      [
        "id" => $id,
        "user_id" => $_SESSION['user']
      ]
    );
  }
}
