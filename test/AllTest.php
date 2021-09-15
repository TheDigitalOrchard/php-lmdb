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

\Tester\Helpers::purge($database);
rmdir($database);