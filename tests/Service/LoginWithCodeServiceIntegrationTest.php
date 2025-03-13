<?php declare(strict_types=1);

namespace HhagLoginWithCode\Tests\Service;

use HhagLoginWithCode\Service\LoginWithCodeService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupCollection;
use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;

class LoginWithCodeServiceIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository<CustomerCollection>
     */
    protected EntityRepository $customerRepository;

    protected Context $context;

    protected LoginWithCodeService $loginWithCodeService;

    protected function setUp(): void
    {
        /** @var EntityRepository<CustomerCollection> $cr */
        $cr = $this->getContainer()->get('customer.repository');
        $this->customerRepository = $cr;
        $this->context = Context::createDefaultContext();

        $this->loginWithCodeService = new LoginWithCodeService($this->customerRepository, $this->context);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCanCreateCustomer(): void
    {
        $email = 'test@example.com';

        $this->customerRepository->create($this->setCustomerData(), $this->context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $email));

        $customerExists = $this->customerRepository->search($criteria, $this->context)->getTotal() > 0;
        static::assertTrue($customerExists);
    }

    public function testGetCustomerByMail(): void
    {
        $email = 'test@example.com';
        $this->customerRepository->create($this->setCustomerData(), $this->context);

        $customer = $this->loginWithCodeService->getCustomerByEmail($email);

        static::assertNotNull($customer);
        static::assertEquals($email, $customer->getEmail());
    }

    private function getValidSalesChannelId(): ?string
    {
        /** @var EntityRepository<SalesChannelCollection> $salesChannelRepo */
        $salesChannelRepo = $this->getContainer()->get('sales_channel.repository');
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->setLimit(1);

        return $salesChannelRepo->searchIds($criteria, $context)->firstId();
    }

    private function getValidCustomerGroupId(): ?string
    {
        /** @var EntityRepository<CustomerGroupCollection> $customerGroupRepo */
        $customerGroupRepo = $this->getContainer()->get('customer_group.repository');
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->setLimit(1);

        return $customerGroupRepo->searchIds($criteria, $context)->firstId();
    }

    /** @return array<int, array<string, mixed>> */
    private function setCustomerData(): array
    {
        $email = 'test@example.com';
        $customerId = Uuid::randomHex();
        $addressId = Uuid::randomHex();

        return [[
            'id' => $customerId,
            'email' => $email,
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'defaultPaymentMethodId' => $this->getValidPaymentMethodId(),
            'groupId' => $this->getValidCustomerGroupId(),
            'salesChannelId' => $this->getValidSalesChannelId(),
            'defaultBillingAddress' => [
                'id' => $addressId,
                'lastName' => 'Mustermann',
                'firstName' => 'Max',
                'countryId' => $this->getValidCountryId(),
                'street' => 'Teststraße 1',
                'zipcode' => '12345',
                'city' => 'Teststadt',
            ],
            'defaultShippingAddressId' => $addressId,
            'activeShippingAddressId' => $addressId,
            'customerNumber' => '1001',
            'password' => 'test1234',
        ],
        ];
    }
}
