<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class MailController extends Controller
{
    public function database()
    {

        $httpClient = new GuzzleAdapter(new Client());

// In these examples, fetch API key from environment variable
$sparky = new SparkPost($httpClient, ["key" => getenv('SPARKPOST_API_KEY')]);

// put your own sending domain and test recipient address here
$sending_domain = getenv('SPARKPOST_SENDING_DOMAIN');
$sending_email = getenv('SPARKPOST_SENDING_EMAIL');
$target = "rustedchip@gmail.com";

$promise = $sparky->transmissions->post([
    'content' => [
        'from' => [
            'name' => 'rustedchip',
            'email' => $sending_email,
        ],
        'subject' => 'First Mailing From PHP',
        'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
        'text' => 'Congratulations, {{name}}! You just sent your very first mailing!',
    ],
    'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
    'recipients' => [
        [
            'address' => [
                'name' => 'YOUR_NAME',
                'email' => $target,
            ],
        ],
    ],
]);

try {
    $response = $promise->wait();
    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
} catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";
}




    }
}
