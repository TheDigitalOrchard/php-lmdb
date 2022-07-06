<?php

use Tester\Assert;

$databaseEnv = require __DIR__ . "/bootstrap.php";
$transaction = $databaseEnv->newTransaction();
$handle = $transaction->getHandle();
$handle->put("foo", "bar");
$transaction->commit();

$transaction = $databaseEnv->newTransaction(true);
$handle = $transaction->getHandle();
Assert::same("bar", $handle->get("foo"));