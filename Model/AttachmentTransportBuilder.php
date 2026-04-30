<?php
/**
 * Aimane Couissi - https://aimanecouissi.com
 * Copyright © Aimane Couissi 2026–present. All rights reserved.
 * Licensed under the MIT License. See LICENSE for details.
 */

declare(strict_types=1);

namespace AimaneCouissi\MailTransportBuilderAttachment\Model;

use AimaneCouissi\MailTransportBuilderAttachment\Api\AttachmentTransportBuilderInterface;
use AimaneCouissi\MailTransportBuilderAttachment\Api\Data\AttachmentInterface;
use AimaneCouissi\MailTransportBuilderAttachment\Exception\FileNotReadableException;
use AimaneCouissi\MailTransportBuilderAttachment\Model\Mail\MultipartMimeMessage;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

class AttachmentTransportBuilder extends TransportBuilder implements AttachmentTransportBuilderInterface
{
    /** @var AttachmentInterface[] */
    private array $attachments = [];

    /** @var array<string, mixed> */
    private array $messageData = [];

    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param EmailMessageInterfaceFactory $emailMessageFactory
     * @param MimePartInterfaceFactory $mimePartFactory
     * @param AddressConverter $addressConverter
     * @param AttachmentFactory $attachmentFactory
     */
    public function __construct(
        FactoryInterface                              $templateFactory,
        MessageInterface                              $message,
        SenderResolverInterface                       $senderResolver,
        ObjectManagerInterface                        $objectManager,
        TransportInterfaceFactory                     $mailTransportFactory,
        private readonly EmailMessageInterfaceFactory $emailMessageFactory,
        private readonly MimePartInterfaceFactory     $mimePartFactory,
        private readonly AddressConverter             $addressConverter,
        private readonly AttachmentFactory            $attachmentFactory,
    )
    {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory,
            null,
            $emailMessageFactory,
            null,
            $mimePartFactory,
            $addressConverter
        );
    }

    /**
     * @inheritDoc
     */
    public function addAttachmentFromFile(
        string  $filePath,
        ?string $filename = null,
        ?string $mimeType = null
    ): static
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new FileNotReadableException(
                __('Attachment file "%1" does not exist or is not readable.', $filePath)
            );
        }
        return $this->addAttachment(
            (string)file_get_contents($filePath),
            $filename ?? basename($filePath),
            $mimeType ?? (mime_content_type($filePath) ?: 'application/octet-stream')
        );
    }

    /**
     * @inheritDoc
     */
    public function addAttachment(
        string  $content,
        string  $filename,
        string  $mimeType = 'application/pdf',
        ?string $encoding = null
    ): static
    {
        if ($content === '') {
            return $this;
        }
        $this->attachments[] = $this->attachmentFactory->create([
            'content' => $content,
            'filename' => $filename,
            'mimeType' => $mimeType,
            'encoding' => $encoding ?? MimeInterface::ENCODING_BASE64,
        ]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clearAttachments(): static
    {
        $this->attachments = [];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @inheritDoc
     */
    public function addTo($address, $name = ''): static
    {
        $this->addAddressByType('to', $address, $name);
        return $this;
    }

    /**
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     * @return void
     */
    private function addAddressByType(string $addressType, string|array $email, ?string $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $converted = $this->addressConverter->convertMany($email);
        $this->messageData[$addressType] = isset($this->messageData[$addressType])
            ? array_merge($this->messageData[$addressType], $converted)
            : $converted;
    }

    /**
     * @inheritDoc
     */
    public function addCc($address, $name = ''): static
    {
        $this->addAddressByType('cc', $address, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBcc($address): static
    {
        $this->addAddressByType('bcc', $address);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setReplyTo($email, $name = null): static
    {
        $this->addAddressByType('replyTo', $email, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFromByScope($from, $scopeId = null): static
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function prepareMessage(): static
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();
        $partType = $template->isPlain() ? MimeInterface::TYPE_TEXT : MimeInterface::TYPE_HTML;
        $bodyPart = $this->mimePartFactory->create([
            'content' => $content,
            'type' => $partType,
        ]);
        $attachmentParts = array_map(
            fn(AttachmentInterface $a) => $this->mimePartFactory->create([
                'content' => $a->getContent(),
                'type' => $a->getMimeType(),
                'fileName' => $a->getFilename(),
                'encoding' => $a->getEncoding(),
                'disposition' => MimeInterface::DISPOSITION_ATTACHMENT,
            ]),
            $this->attachments
        );
        $this->messageData['body'] = new MultipartMimeMessage([$bodyPart, ...$attachmentParts]);
        $this->messageData['subject'] = html_entity_decode($template->getSubject(), ENT_QUOTES);
        $this->message = $this->emailMessageFactory->create($this->messageData);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function reset(): static
    {
        parent::reset();
        $this->messageData = [];
        $this->attachments = [];
        return $this;
    }
}
