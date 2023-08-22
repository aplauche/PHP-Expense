<?php

declare(strict_types=1);


namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\TransactionService;

class HomeController
{

  public function __construct(private TemplateEngine $view, private TransactionService $transactionService)
  {
  }

  public function home()
  {

    $page = $_GET['p'] ?? 1;
    // make sure it is a number
    $page = (int) $page;
    // TODO: add control of length to page
    $length = 3;
    $offset = ($page - 1) * $length;

    $searchTerm = $_GET['s'] ?? null;


    $results = $this->transactionService->getUserTransactions(
      $length,
      $offset
    );

    $transactions = $results['transactions'];
    $count = $results['transactionCount'];

    $lastPage = ceil($count / $length);

    // create an array of page numbers
    $pages = $lastPage ? range(1, $lastPage) : [];

    // transfrom to query params
    $pageLinks = array_map(
      fn ($num) => http_build_query([
        's' => $searchTerm,
        'p' => $num
      ]),
      $pages
    );

    echo $this->view->render("index.php", [
      'transactions' => $transactions,
      'currentPage' => $page,
      // if an item has null value it will be excluded from query
      'previousPageQuery' => http_build_query(['s' => $searchTerm, 'p' => $page - 1]),
      'lastPage' => $lastPage,
      'nextPageQuery' => http_build_query(['s' => $searchTerm, 'p' => $page + 1]),
      'pageLinks' => $pageLinks,
      'searchTerm' => $searchTerm
    ]);
  }
}
