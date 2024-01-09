<?php

namespace LunaQL\Builder;

use Exception;
use GuzzleHttp\Client;
use LunaQL\Config\DocumentConfig;
use LunaQL\Exceptions\Handler;

class DocumentBuilder {
    /**
     * Create a new document builder.
     */
    public function __construct(
        private DocumentConfig $config
    ) {}

    /**
     * Insert data into the collection.
     */
    public function into(string $collection) {
        $client = new Client();

        try {
            $response = $client->request(
                "PUT", "{$this->config->getEndpoint()}/{$collection}" . ($this->config->getType() == 'documents' ? '/batch' : ''), [
                    "json" => $this->config->getData(),
                    "headers" => [ "Authorization" => "Bearer " . $this->config->getToken() ]
                ]
            );

            return json_decode((string) $response->getBody());
        } catch(Exception $exception) {
            throw Handler::handle($exception);
        }
    }
}
