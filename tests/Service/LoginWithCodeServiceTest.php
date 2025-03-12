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
    }
    public function testServiceIsInstantiable(): void{
        $service = new LoginWithCodeService($this->customerRepoMock, $this->contextMock);

        $this->assertInstanceOf(LoginWithCodeService::class, $service);
    }
    public function testCodeIsInteger(){
        $code = (new LoginWithCodeService($this->customerRepoMock, $this->contextMock))->createCode();
        $this->assertIsInt($code);
    }
}