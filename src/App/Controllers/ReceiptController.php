<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{TransactionService, ReceiptService};

class ReceiptController
{
  public function __construct(
    private TemplateEngine $view,
    private TransactionService $transactionService,
    private ReceiptService $receiptService
  ) {
  }

  public function uploadView(array $params)
  {
    $transaction = $this->transactionService->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    echo $this->view->render("receipts/create.php");
  }

  public function upload(array $params)
  {
    $transaction = $this->transactionService->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    $receiptFile = $_FILES['receipt'] ?? null;

    $this->receiptService->validateFile($receiptFile);
    $this->receiptService->upload($receiptFile, $transaction['id']);

    redirectTo("/");
  }

  public function download(array $params)
  {
    // Check to make sure transaction in url exists
    $transaction = $this->transactionService->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    // Check to make sure receipt in url exists
    $receipt = $this->receiptService->getReceipt($params['receipt']);

    if (!$receipt) {
      redirectTo("/");
    }

    // make sure receipt is actually associated with this transaction
    if ($receipt["transaction_id"] !== $transaction["id"]) {
      redirectTo('/');
    }

    $this->receiptService->read($receipt);
  }

  public function delete(array $params)
  {
    // Check to make sure transaction in url exists
    $transaction = $this->transactionService->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    // Check to make sure receipt in url exists
    $receipt = $this->receiptService->getReceipt($params['receipt']);

    if (!$receipt) {
      redirectTo("/");
    }

    // make sure receipt is actually associated with this transaction
    if ($receipt["transaction_id"] !== $transaction["id"]) {
      redirectTo('/');
    }

    $this->receiptService->delete($receipt);

    redirectTo('/');
  }
}
