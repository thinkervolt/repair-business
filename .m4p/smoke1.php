<?php
$u = \App\Models\User::first();
echo 'user=' . ($u ? $u->email : 'NONE') . "\n";

$payload = [
    'customer_company' => 'Acme Test Co',
    'customer_name' => 'John Doe',
    'customer_phone' => '555-0100',
    'customer_email' => 'jd@example.com',
    'customer_address' => '1 Main St',
    'tax_porcentage' => 8,
];
$token = \App\Models\PersonalAccessToken::orderBy('id', 'DESC')->first();
echo 'token=' . ($token ? (int)$token->tokenable_id : 'NONE') . "\n";

$client = new \Illuminate\Support\Facades\Http;
$http = $client::asJson();
$user = $u->id ?? 1 | 1;
