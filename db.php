<?php
use iggyvolz\lmdb\Environment;
require_once __DIR__ . "/vendor/autoload.php";
$db = $_GET["db"] ?? null;
$transaction = (new Environment(__DIR__ . "/db", numDatabases: is_null($db) ? 0 : 1))->newTransaction(readOnly: true);
$handle = $transaction->getHandle($db);
?>
<html>
<head>
<style>
table,th,td{border: 1px solid black;}
</style>
<title>List Databases</title>
</head>
<body>
<<?= is_null($db) ? "ul" : "table" ?>>
<?php
if(!is_null($db)) {
    ?>
    <tr>
    <th>Key</th>
    <th>Value</th>
    </tr>
    <?php
}
foreach($handle->all() as $key => $value) {
    $doc = new DOMDocument();
    if(is_null($db)) {
        $doc->appendChild($li = $doc->createElement("li"));
        $li->appendChild($a = $doc->createElement("a"));
        $a->setAttribute("href", "db.php?db=" . urlencode($key));
        $a->textContent = $key;
    } else {
        $doc->appendChild($tr = $doc->createElement("tr"));
        $tr->appendChild($keyCell = $doc->createElement("td"));
        $tr->appendChild($valueCell = $doc->createElement("td"));
        $keyCell->textContent = $key;
        $valueCell->textContent = $value;
    }
    echo $doc->saveHTML();
}
?>
</<?= is_null($db) ? "ul" : "table" ?>>
</body>
</html>
