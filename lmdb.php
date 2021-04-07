<?php

use iggyvolz\lmdb\LMDB;
use iggyvolz\lmdb\Environment;
require_once __DIR__ . "/vendor/autoload.php";
$transaction = (new Environment(__DIR__ . "/db", numDatabases: 1))->newTransaction();
$handle = $transaction->getHandle("foox", flags: LMDB::CREATE);
var_dump($handle->get("foo"));
$handle->put("foo", "bar");
$handle->put("foop", "bar2");
var_dump($handle->get("foo"));
var_dump(iterator_to_array($handle->all()));
$transaction->commit();
