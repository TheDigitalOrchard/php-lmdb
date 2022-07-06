<?php


use Tester\Environment;
use Tester\Helpers;

require __DIR__ . "/../vendor/autoload.php";
Environment::setup();

$database = __DIR__ . "/db_" . getmypid();

if(is_dir($database)) {
    throw new LogicException("Database folder collision");
}

mkdir($database);
register_shutdown_function(function() use ($database) {
    Helpers::purge($database);
    rmdir($database);
});

try {
    return new \iggyvolz\lmdb\Environment($database);
} catch(\FFI\Exception $e) {
    if($e->getMessage() === "Failed loading 'liblmdb.so'") {
        Tester\Environment::skip("LMDB not installed");
    } else {
        throw $e;
    }
}
