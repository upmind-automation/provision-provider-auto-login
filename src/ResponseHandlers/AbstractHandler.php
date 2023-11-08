<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\ResponseHandlers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;

/**
 * Handler to parse data from a PSR-7 response body.
 */
abstract class AbstractHandler
{
    /**
     * @var \Psr\Http\Message\ResponseInterface $response
     */
    protected $response;

    /**
     * Raw response body text.
     *
     * @var string|null $body
     */
    protected $body;

    /**
     * Parsed response body data.
     *
     * @var array|null $data
     */
    protected $data;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get parsed response body data.
     *
     * @return mixed|null
     */
    public function getData(?string $property = null)
    {
        $this->parse();

        if ($property) {
            return Arr::get((array)$this->data, $property);
        }

        return $this->data;
    }

    /**
     * Get trimmed response body as a string.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body ?? ($this->body = trim($this->response->getBody()->__toString()));
    }

    /**
     * @throws CannotParseResponse If response is an error or body is invalid
     */
    protected function parse(bool $assertSuccess = true): void
    {
        if (isset($this->data)) {
            // already parsed
            return;
        }

        if ($assertSuccess) {
            $this->assertSuccess();
        }

        if ($this->bodyIsJson()) {
            $this->parseJson();
            return;
        }

        if ($this->bodyIsText()) {
            $this->parseText();
            return;
        }

        throw new CannotParseResponse('Unable to parse response of this content type');
    }

    /**
     * Attempt to parse the response body JSON into a data array.
     *
     * @throws CannotParseResponse
     *
     * @return void
     */
    protected function parseJson(): void
    {
        if (!$data = json_decode($this->getBody(), true)) {
            throw new CannotParseResponse('Invalid JSON response');
        }

        $this->data = $data;
    }

    /**
     * Attempt to parse the response body text into a data array.
     *
     * @throws CannotParseResponse
     *
     * @return void
     */
    protected function parseText(): void
    {
        if (!$body = $this->getBody()) {
            throw new CannotParseResponse('Empty text response');
        }

        parse_str($body, $data);

        $this->data = $data;
    }

    /**
     * Determine whether the given response is JSON.
     *
     * @return bool
     */
    protected function bodyIsJson(): bool
    {
        $contentType = $this->response->getHeaderLine('Content-Type');

        return Str::contains($contentType, ['application/json', '+json']);
    }

    /**
     * Determine whether the given response is plaintext.
     *
     * @return bool
     */
    protected function bodyIsText(): bool
    {
        $contentType = $this->response->getHeaderLine('Content-Type');

        return empty(trim($contentType))
            || Str::contains($contentType, ['text/html', 'text/plain', 'application/x-www-form-urlencoded']);
    }

    /**
     * Determine if the http response code is 2xx.
     */
    public function isSuccess(): bool
    {
        $httpCode = $this->response->getStatusCode();

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Determine if the http response code is not 2xx.
     */
    public function isError(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * @throws CannotParseResponse If response http code is not 2xx
     */
    public function assertSuccess(): void
    {
        if ($this->isError()) {
            $errorMessage = sprintf('%s %s', $this->response->getStatusCode(), $this->response->getReasonPhrase());

            try {
                $this->parse(false);

                $responseError = $this->getData('error_message')
                    ?? $this->getData('message')
                    ?? $this->getData('error');
                $errorMessage = $responseError ?: $errorMessage;
            } catch (\Throwable $e) {
                // ignore
            }

            throw new CannotParseResponse(sprintf('Service error: %s', $errorMessage));
        }
    }
}
