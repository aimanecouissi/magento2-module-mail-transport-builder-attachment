# AimaneCouissi_MailTransportBuilderAttachment

[![Latest Stable Version](http://poser.pugx.org/aimanecouissi/module-mail-transport-builder-attachment/v)](https://packagist.org/packages/aimanecouissi/module-mail-transport-builder-attachment) [![Total Downloads](http://poser.pugx.org/aimanecouissi/module-mail-transport-builder-attachment/downloads)](https://packagist.org/packages/aimanecouissi/module-mail-transport-builder-attachment) [![Magento Version](https://img.shields.io/badge/magento-2.4.8%2B-E68718)](https://packagist.org/packages/aimanecouissi/module-mail-transport-builder-attachment) [![License](http://poser.pugx.org/aimanecouissi/module-mail-transport-builder-attachment/license)](https://packagist.org/packages/aimanecouissi/module-mail-transport-builder-attachment) [![PHP Version Require](http://poser.pugx.org/aimanecouissi/module-mail-transport-builder-attachment/require/php)](https://packagist.org/packages/aimanecouissi/module-mail-transport-builder-attachment)

Extends Magento's mail transport builder with email attachment support. The module provides an attachment-capable
transport builder interface for adding raw content or file-based attachments before a message is sent.

## Installation

```bash
composer require aimanecouissi/module-mail-transport-builder-attachment
bin/magento module:enable AimaneCouissi_MailTransportBuilderAttachment
bin/magento setup:upgrade
bin/magento cache:flush
```

## Usage

Inject `AttachmentTransportBuilderInterface` where a Magento mail transport builder is needed. Use `addAttachment()` for
raw content and `addAttachmentFromFile()` for readable file paths.

Call the attachment methods before `getTransport()`. Queued attachments are added to the generated MIME message and
cleared when the builder resets after transport creation.

## Uninstall

```bash
bin/magento module:disable AimaneCouissi_MailTransportBuilderAttachment
composer remove aimanecouissi/module-mail-transport-builder-attachment
bin/magento setup:upgrade
bin/magento cache:flush
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for all recent changes.

## License

[MIT](LICENSE)
