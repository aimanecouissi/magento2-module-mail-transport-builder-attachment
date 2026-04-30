<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Model;

use AimaneCouissi\MailTransportBuilderAttachment\Api\Data\AttachmentInterface;
use Magento\Framework\Mail\MimeInterface;

class Attachment implements AttachmentInterface
{
    /**
     * @param string $content
     * @param string $filename
     * @param string $mimeType
     * @param string $encoding
     */
    public function __construct(
        private readonly string $content,
        private readonly string $filename,
        private readonly string $mimeType = 'application/pdf',
        private readonly string $encoding = MimeInterface::ENCODING_BASE64,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @inheritDoc
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
