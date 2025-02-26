<?php declare(strict_types=1);

namespace HhagLoginWithCode\Extension;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new IntField('login_code', 'loginCode'))
        );
    }

    public function getEntityName(): string
    {
        return CustomerDefinition::class;
    }
}
