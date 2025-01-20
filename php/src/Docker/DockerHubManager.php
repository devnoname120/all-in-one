<?php

namespace AIO\Docker;

use AIO\ContainerDefinitionFetcher;
use AIO\Data\ConfigurationManager;
use GuzzleHttp\Client;

readonly class DockerHubManager {
    private Client $guzzleClient;

    public function __construct(
    ) {
        $this->guzzleClient = new Client();
    }

    public function GetLatestDigestOfTag(string $name, string $tag) : ?string {
        $cacheKey = 'dockerhub-manifest-' . $name . $tag;

        $cachedVersion = apcu_fetch($cacheKey);
        if($cachedVersion !== false && is_string($cachedVersion)) {
            return $cachedVersion;
        }

        // If one of the links below should ever become outdated, we can still upgrade the mastercontainer via the webinterface manually by opening '/api/docker/getwatchtower'

        try {
            $manifestBaseApiUrl = str_replace('ghcr.io/devnoname120/', 'https://ghcr.io/v2/devnoname120/', $name);
            $manifestRequest = $this->guzzleClient->request(
                'GET',
                $manifestBaseApiUrl . '/manifests/' . $tag,
                [
                    'headers' => [
                        'Accept' => 'application/vnd.oci.image.index.v1+json,application/vnd.docker.distribution.manifest.list.v2+json,application/vnd.docker.distribution.manifest.v2+json',
                    ],
                ]
            );

            $responseBody = $manifestRequest->getBody()->getContents();
            $responseData = json_decode($responseBody, true);


            if (json_last_error() === JSON_ERROR_NONE && isset($responseData['config']) && $responseData['config'] !== null && isset($responseData['config']['digest']) && $responseData['config']['digest'] !== null) {
               $latestVersion = $responseData['config']['digest'];
               apcu_add($cacheKey, $latestVersion, 600);
               return $latestVersion;
            }

            error_log('Response Data: ' . print_r($responseData, true));
            error_log('Could not get digest of container ' . $name . ':' . $tag);
            return null;
        } catch (\Exception $e) {
            error_log('Could not get digest of container ' . $name . ':' . $tag . ' ' . $e->getMessage());
            return null;
        }
    }
}
