<?php

$target = 'routes/api.php';
$base = file_get_contents($targeterin);
$frag = file_get_contents('.m4p/routes_part.php');

$anchor = "Route::post('/barcode/invoice', [BarcodeController::class, 'scanInvoice'])";
$pos = strpos($base, $anchor);
if ($pos === false) {
    fwrite(STDERR, "ANCHOR NOT FOUND\n");
    exit(1);
}
$end = strpos($base, "\n", $pos);
if ($end === false) {
    fwrite(STDERR, "NEWLINE NOT FOUND\n");
    exit(1);
}

$merged = substr($base, 0, $end + 1) . $frag . substr($base, $end + 1);
file_put_contents($target, $merged);
echo "SPLICED OK; new length = " . strlen($merged) . "\n";
