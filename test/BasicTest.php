<?php

use Tester\Assert;

require __DIR__ . "/../vendor/autoload.php";
\Tester\Environment::setup();

$database = __DIR__ . "/db_" . getmypid();

if(is_dir($database)) {
    throw new LogicException("Database folder collision");
}

mkdir($database);

$databaseEnv = new \iggyvolz\lmdb\Environment($database);

$transaction = $databaseEnv->newTransaction();
$handle = $transaction->getHandle();
$handle->put("foo", "bar");
$transaction->commit();

$transaction = $databaseEnv->newTransaction(true);
$handle = $transaction->getHandle();
Assert::same("bar", $handle->get("foo"));
$transaction->commit();