<?php

namespace VCR;

use VCR\Storage\Storage;

/**
 * A Cassette records and plays back pairs of Requests and Responses in a Storage.
 */
class Cassette
{
    /**
     * Casette name.
     *
     * @var string
     */
    protected $name;

    /**
     * VCR configuration.
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Storage used to store records and request pairs.
     *
     * @var Storage<array>
     */
    protected $storage;

    /**
     * Creates a new cassette.
     *
     * @param string         $name    name of the cassette
     * @param Configuration  $config  configuration to use for this cassette
     * @param Storage<array> $storage storage to use for requests and responses
     *
     * @throws \VCR\VCRException if cassette name is in an invalid format
     */
    public function __construct(string $name, Configuration $config, Storage $storage)
    {
        $this->name = $name;
        $this->config = $config;
        $this->storage = $storage;
    }

    /**
     * Returns true if a response was recorded for specified request.
     *
     * @param Request $request request to check if it was recorded
     *
     * @return bool true if a response was recorded for specified request
     */
    public function hasResponse(Request $request, int $index = 0): bool
    {
        return null !== $this->playback($request, $index);
    }

    /**
     * Returns a response for given request or null if not found.
     *
     * @param Request $request request
     *
     * @return Response|null response for specified request
     */
    public function playback(Request $request, int $index = 0): ?Response
    {
        foreach ($this->storage as $recording) {
            $storedRequest = Request::fromArray($recording['request']);

            // Support legacy cassettes which do not have the 'index' key by setting the index to the searched one to
            // always match this record if the request matches
            $recording['index'] = $recording['index'] ?? $index;

            if ($storedRequest->matches($request, $this->getRequestMatchers()) && $index == $recording['index']) {
                return Response::fromArray($recording['response']);
            }
        }

        return null;
    }

    /**
     * Records a request and response pair.
     *
     * @param Request  $request  request to record
     * @param Response $response response to record
     */
    public function record(Request $request, Response $response, int $index = 0): void
    {
        if ($this->hasResponse($request, $index)) {
            return;
        }

        $recording = [
            'request' => $request->toArray(),
            'response' => $response->toArray(),
            'index' => $index,
        ];

        $this->storage->storeRecording($recording);
    }

    /**
     * Returns the name of the current cassette.
     *
     * @return string current cassette name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns true if the cassette was created recently.
     */
    public function isNew(): bool
    {
        return $this->storage->isNew();
    }

    /**
     * Returns a list of callbacks to configured request matchers.
     *
     * @return callable[] list of callbacks to configured request matchers
     */
    protected function getRequestMatchers(): array
    {
        return $this->config->getRequestMatchers();
    }
}
