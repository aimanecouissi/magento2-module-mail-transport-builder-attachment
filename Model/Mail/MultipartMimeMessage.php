<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Model\Mail;

use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterface;
use Magento\Framework\Mail\MimePartInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\TextPart;

class MultipartMimeMessage implements MimeMessageInterface
{
    /** @var MimePartInterface[] */
    private array $parts;

    /** @var Message */
    private Message $mimeMessage;

    /**
     * @param MimePartInterface[] $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
        $symfonyParts = array_map(static fn(MimePartInterface $part) => $part->getMimePart(), $parts);
        if ($symfonyParts === []) {
            $this->mimeMessage = new Message();
            return;
        }
        if (count($symfonyParts) === 1 && $symfonyParts[0] instanceof TextPart) {
            $this->mimeMessage = new Message($symfonyParts[0]->getHeaders(), $symfonyParts[0]);
            return;
        }
        $this->mimeMessage = new Message(null, new MixedPart(...$symfonyParts));
    }

    /**
     * @inheritDoc
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @inheritDoc
     */
    public function isMultiPart(): bool
    {
        return count($this->parts) > 1;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(string $endOfLine = MimeInterface::LINE_END): string
    {
        return str_replace("\r\n", $endOfLine, $this->mimeMessage->toString());
    }

    /**
     * @inheritDoc
     */
    public function getPartHeadersAsArray(int $partNum): array
    {
        if (!isset($this->parts[$partNum])) {
            return [];
        }
        $headersArray = [];
        foreach ($this->parts[$partNum]->getMimePart()->getHeaders()->toArray() as $header) {
            $headersArray[$header->getName()] = $header->getBodyAsString();
        }
        return $headersArray;
    }

    /**
     * @inheritDoc
     */
    public function getPartHeaders(int $partNum, string $endOfLine = MimeInterface::LINE_END): string
    {
        if (!isset($this->parts[$partNum])) {
            return '';
        }
        return str_replace(
            "\r\n",
            $endOfLine,
            $this->parts[$partNum]->getMimePart()->getHeaders()->toString()
        );
    }

    /**
     * @inheritDoc
     */
    public function getPartContent(int $partNum, string $endOfLine = MimeInterface::LINE_END): string
    {
        if (!isset($this->parts[$partNum])) {
            return '';
        }
        return str_replace(
            "\r\n",
            $endOfLine,
            $this->parts[$partNum]->getMimePart()->bodyToString()
        );
    }

    /**
     * Returns the underlying Symfony MIME message.
     *
     * @return Message
     */
    public function getMimeMessage(): Message
    {
        return $this->mimeMessage;
    }
}
