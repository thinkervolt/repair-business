<?php

$parts = [
    '.m4p/part1.php',
    '.m4p/part2.php',
    '.m4p/part3.php',
    '.m4p/part4.php',
    '.m4p/part5.php',
];

$out = "app/Http/Controllers/Api/V1/InvoiceController.php";
$data = "<?php\n\n";
foreach ($parts as $p) {
    $c = file_get_contents($p);
    $c = preg_replace('/^<\?php\s*/s', '', $c);
    $data .= rtrim($c) . "\n";
}
$data .= "\n";
file_put_contents($out, $data);
echo "WROTE " . strlen($data) . " bytes\n";
