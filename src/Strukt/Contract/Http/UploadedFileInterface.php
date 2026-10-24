<?php

namespace Strukt\Contract\Http;

/**
 * Describes a file received through a multipart form submission.
 */
interface UploadedFileInterface
{
    /**
     * Reads the client-supplied filename.
     *
     * @return string Filename as sent by the client.
     */
    public function name(): string;

    /**
     * Reads the payload size in bytes.
     *
     * @return int Payload length in bytes, or 0 when the upload failed.
     */
    public function size(): int;

    /**
     * Reads the detected media type.
     *
     * @return string Media type, or `application/octet-stream` when undetected.
     */
    public function mime(): string;

    /**
     * Reads the lowercase file extension.
     *
     * @return string Extension without a leading dot, or an empty string.
     */
    public function extension(): string;

    /**
     * Reads the PHP upload error code.
     *
     * @return int One of the `UPLOAD_ERR_*` constants.
     */
    public function error(): int;

    /**
     * Checks whether the upload completed without error.
     *
     * @return bool True when the file is readable and error-free.
     */
    public function isValid(): bool;

    /**
     * Reads the temporary path backing the upload.
     *
     * @return string Temporary path, or an empty string when unavailable.
     */
    public function path(): string;

    /**
     * Reads the payload.
     *
     * @return string Payload bytes, or an empty string when the upload failed.
     */
    public function contents(): string;

    /**
     * Moves the upload to its final destination.
     *
     * @param string $target Destination path.
     * @return static This upload.
     *
     * @throws \RuntimeException When the upload cannot be moved.
     */
    public function move(string $target): static;
}
