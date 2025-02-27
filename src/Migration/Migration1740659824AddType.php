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

    public function createMailTemplate(): void
    {

    }
}