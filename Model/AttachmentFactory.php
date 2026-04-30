<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Model;

use Magento\Framework\ObjectManagerInterface;

class AttachmentFactory
{
    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    /**
     * @param array{content: string, filename: string, mimeType?: string, encoding?: string} $data
     * @return Attachment
     */
    public function create(array $data): Attachment
    {
        return $this->objectManager->create(Attachment::class, $data);
    }
}
