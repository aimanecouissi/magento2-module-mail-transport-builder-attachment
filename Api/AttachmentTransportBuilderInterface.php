<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Api;

use AimaneCouissi\MailTransportBuilderAttachment\Exception\FileNotReadableException;

/**
 * @api
 * @since 1.0.0
 */
interface AttachmentTransportBuilderInterface
{
    /**
     * Adds an attachment from raw content.
     *
     * @param string $content
     * @param string $filename
     * @param string $mimeType
     * @param string|null $encoding
     * @return static
     */
    public function addAttachment(string $content, string $filename, string $mimeType = 'application/pdf', ?string $encoding = null): static;

    /**
     * Adds an attachment from a file path.
     *
     * @param string $filePath
     * @param string|null $filename
     * @param string|null $mimeType
     * @return static
     * @throws FileNotReadableException
     */
    public function addAttachmentFromFile(string $filePath, ?string $filename = null, ?string $mimeType = null): static;

    /**
     * Removes all queued attachments.
     *
     * @return static
     */
    public function clearAttachments(): static;

    /**
     * Returns all queued attachments.
     *
     * @return array
     */
    public function getAttachments(): array;
}
