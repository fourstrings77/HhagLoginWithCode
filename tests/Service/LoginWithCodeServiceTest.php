<?php

namespace HhagLoginWithCode\Tests\Service;

use HhagLoginWithCode\Service\LoginWithCodeService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class LoginWithCodeServiceTest extends TestCase
{
    protected $loginWithCodeService;
    protected $customerRepoMock;
    protected $contextMock;

    protected function setUp(): void{
        $this->customerRepoMock = $this->createMock(EntityRepository::class);
        $this->contextMock = $this->createMock(Context::class);

        $this->loginWithCodeService = new LoginWithCodeService($this->customerRepoMock, $this->contextMock);
    }
    public function testServiceIsInstantiable(): void{
        $this->assertInstanceOf(LoginWithCodeService::class, $this->loginWithCodeService);
    }
    public function testCodeIsInteger(){
        $code = $this->loginWithCodeService->createCode();

        $this->assertEquals(6, strlen($code));
        $this->assertNotNull($code);
        $this->assertIsInt($code);
    }
}