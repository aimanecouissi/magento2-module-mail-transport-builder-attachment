<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Exception;

/**
 * @api
 * @since 1.0.0
 */
class FileNotReadableException extends LocalizedException
{
    /**
     * @inheritDoc
     */
    public function __construct(Phrase $phrase, Exception $cause = null, int $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
    }
}
