<?php

namespace Strukt\Contract\Http;

/**
 * Describes a file offered to the client as an attachment or inline payload.
 */
interface DownloadInterface
{
    /**
     * Reads the filename presented to the client.
     *
     * @return string Suggested filename, including its extension.
     */
    public function filename(): string;

    /**
     * Assigns the filename presented to the client.
     *
     * @param string $filename Suggested filename.
     * @return static This download.
     *
     * @throws \InvalidArgumentException When the filename is empty.
     */
    public function setFilename(string $filename): static;

    /**
     * Reads the media type.
     *
     * @return string Media type such as `application/pdf`.
     */
    public function mime(): string;

    /**
     * Assigns the media type.
     *
     * @param string $mime Media type.
     * @return static This download.
     *
     * @throws \InvalidArgumentException When the media type is empty.
     */
    public function setMime(string $mime): static;

    /**
     * Reads the payload size in bytes.
     *
     * @return int Payload length in bytes.
     */
    public function size(): int;

    /**
     * Reads the payload.
     *
     * @return string Payload bytes, or an empty string when the payload is unreadable.
     */
    public function content(): string;

    /**
     * Reads the content-disposition mode.
     *
     * @return string Either `attachment` or `inline`.
     */
    public function disposition(): string;

    /**
     * Requests that the client render the payload in place.
     *
     * @return static This download.
     */
    public function inline(): static;

    /**
     * Requests that the client save the payload as an attachment.
     *
     * @return static This download.
     */
    public function attachment(): static;

    /**
     * Builds the content-disposition header value.
     *
     * @return string Header value including filename and disposition mode.
     */
    public function toHeaderValue(): string;

    /**
     * Emits the headers and payload to the output stream.
     *
     * @return void
     */
    public function send(): void;
}
