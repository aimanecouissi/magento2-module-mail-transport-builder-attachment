<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Api\Data;

/**
 * @api
 * @since 1.0.0
 */
interface AttachmentInterface
{
    /**
     * Returns the attachment content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Returns the attachment filename.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Returns the attachment MIME type.
     *
     * @return string
     */
    public function getMimeType(): string;

    /**
     * Returns the attachment transfer encoding.
     *
     * @return string
     */
    public function getEncoding(): string;
}
