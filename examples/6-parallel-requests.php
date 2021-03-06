<?php

use Amp\Artax\Response;
use Amp\Loop;

require __DIR__ . '/../vendor/autoload.php';

Loop::run(function () {
    $uris = [
        "https://google.com/",
        "https://github.com/",
        "https://stackoverflow.com/",
    ];

    // Instantiate the HTTP client
    $client = new Amp\Artax\DefaultClient;

    $requestHandler = function (string $uri) use ($client) {
        /** @var Response $response */
        $response = yield $client->request($uri);

        return $response->getBody();
    };

    try {
        $promises = [];

        foreach ($uris as $uri) {
            $promises[$uri] = Amp\call($requestHandler, $uri);
        }

        $bodies = yield $promises;

        foreach ($bodies as $uri => $body) {
            print $uri . " - " . \strlen($body) . " bytes" . PHP_EOL;
        }
    } catch (Amp\Artax\HttpException $error) {
        // If something goes wrong Amp will throw the exception where the promise was yielded.
        // The Client::request() method itself will never throw directly, but returns a promise.
        echo $error;
    }
});
