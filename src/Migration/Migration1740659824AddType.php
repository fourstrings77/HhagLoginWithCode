<?php

declare(strict_types=1);

namespace HhagLoginWithCode\Migration;

use DateTime;
use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1740659824AddType extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1740659824;
    }

    public function update(Connection $connection): void
    {
        $mailTemplateTypeId = $this->createMailTemplateType($connection);
        $this->createMailTemplate($connection, $mailTemplateTypeId);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    public function createMailTemplateType(Connection $connection)
    {
        $typeId = Uuid::randomHex();

        $enLangId = $this->getLanguageByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageByLocale($connection, 'de-DE');

        $englishName = 'Example mail template type name';
        $germanName = 'Beispiel E-Mail Template Name';

        $connection->executeStatement(
            "INSERT IGNORE INTO `mail_template_type`
                (id, technical_name, available_entities, created_at)
            VALUES
                (:id, :technicalName, :availableEntities, :createdAt)
        ",[
            'id' => Uuid::fromHexToBytes($typeId),
            'technicalName' => 'lwc_mail_template_type',
            'availableEntities' => json_encode(['customer' => 'customer']),
            'createdAt' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
          ]
        );


        return $typeId;
    }

    public function createMailTemplate(Connection $connection, $mailTemplateTypeId): void
    {
        $mailTemplateId = Uuid::randomHex();

        $enGbLangId = $this->getLanguageByLocale($connection, 'en-GB');
        $deDeLangId = $this->getLanguageByLocale($connection, 'de-DE');

        $connection->executeStatement("
        INSERT IGNORE INTO `mail_template`
            (id, mail_template_type_id, system_default, created_at)
        VALUES
            (:id, :mailTemplateTypeId, :systemDefault, :createdAt)
        ",[
            'id' => Uuid::fromHexToBytes($mailTemplateId),
            'mailTemplateTypeId' => Uuid::fromHexToBytes($mailTemplateTypeId),
            'systemDefault' => 0,
            'createdAt' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        if (!empty($enGbLangId)) {
            $connection->executeStatement("
            INSERT IGNORE INTO `mail_template_translation`
                (mail_template_id, language_id, sender_name, subject, description, content_html, content_plain, created_at)
            VALUES
                (:mailTemplateId, :languageId, :senderName, :subject, :description, :contentHtml, :contentPlain, :createdAt)
            ",[
                'mailTemplateId' => Uuid::fromHexToBytes($mailTemplateId),
                'languageId' => $enGbLangId,
                'senderName' => '{{ salesChannel.name }}',
                'subject' => 'Example mail template subject',
                'description' => 'Example mail template description',
                'contentHtml' => $this->getContentHtmlEn(),
                'contentPlain' => $this->getContentPlainEn(),
                'createdAt' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }

        if (!empty($deDeLangId)) {
            $connection->executeStatement("
            INSERT IGNORE INTO `mail_template_translation`
                (mail_template_id, language_id, sender_name, subject, description, content_html, content_plain, created_at)
            VALUES
                (:mailTemplateId, :languageId, :senderName, :subject, :description, :contentHtml, :contentPlain, :createdAt)
            ",[
                'mailTemplateId' => Uuid::fromHexToBytes($mailTemplateId),
                'languageId' => $deDeLangId,
                'senderName' => '{{ salesChannel.name }}',
                'subject' => 'Dein Logincode für {{ salesChannel.name }}',
                'description' => 'Basistemplate für den Login-Code',
                'contentHtml' => $this->getContentHtmlDe(),
                'contentPlain' => $this->getContentPlainDe(),
                'createdAt' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }

    }

    private function getContentHtmlEn(): string
    {
        return <<<MAIL
        <div style="font-family:arial; font-size:12px;">
            <p>
                Example HTML content!
            </p>
        </div>
        MAIL;
    }

    private function getContentPlainEn(): string
    {
        return <<<MAIL
        Example plain content!
        MAIL;
    }

    private function getContentHtmlDe(): string
    {
        return <<<MAIL
        <div style="font-family:arial; font-size:12px;">
            <p>
                Beispiel HTML Inhalt!
            </p>
        </div>
        MAIL;
    }

    private function getContentPlainDe(): string
    {
        return <<<MAIL
        Beispiel Plain Inhalt!
        MAIL;
    }

    public function getLanguageByLocale(Connection $connection, string $locale): ?string
    {
        $sql = <<<SQL
        SELECT `language`.`id`
        FROM `language`
        INNER JOIN `locale` ON `locale`.`id` = `language`.`locale_id`
        WHERE `locale`.`code` = :code
        SQL;

        $languageId = $connection->executeQuery($sql, ['code' => $locale])->fetchOne();

        if (empty($languageId)) {
            return null;
        }

        return $languageId;
    }
}