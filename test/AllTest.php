<?php

use Tester\Assert;

$databaseEnv = require __DIR__ . "/bootstrap.php";
$transaction = $databaseEnv->newTransaction();

$conts = [
    "foo" => "bar",
    "bin" => "bak",
    "yin" => "yang"
];
$handle = $transaction->getHandle();
foreach($conts as $key => $value) {
    $handle->put($key, $value);
}
$transaction->commit();

$transaction = $databaseEnv->newTransaction(true);
$handle = $transaction->getHandle();
// Result may be returned in any order - so we should not test the order
Assert::equal($conts, iterator_to_array($handle->all()));
