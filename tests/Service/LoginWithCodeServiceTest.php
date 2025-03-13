<?php declare(strict_types=1);

namespace HhagLoginWithCode\Tests\Service;

use HhagLoginWithCode\Service\LoginWithCodeService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class LoginWithCodeServiceTest extends TestCase
{
    protected LoginWithCodeService $loginWithCodeService;

    /**
     * @var EntityRepository<CustomerCollection>
     */
    protected EntityRepository $customerRepoMock;

    protected Context $contextMock;

    protected function setUp(): void
    {
        $this->customerRepoMock = $this->createMock(EntityRepository::class);
        $this->contextMock = $this->createMock(Context::class);

        $this->loginWithCodeService = new LoginWithCodeService($this->customerRepoMock, $this->contextMock);
    }

    public function testServiceIsInstantiable(): void
    {
        static::assertInstanceOf(LoginWithCodeService::class, $this->loginWithCodeService);
    }

    public function testCodeIsInteger(): void
    {
        $code = $this->loginWithCodeService->createCode();

        static::assertEquals(6, \strlen((string) $code));
        static::assertNotNull($code);
    }
}
