<?php

declare(strict_types=1);

namespace HhagLoginWithCode\Service;

use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Mailer\MailerInterface;

class LoginWithCodeService
{
    /** @var EntityRepository<CustomerCollection> */
    protected EntityRepository $customerRepository;

    protected MailerInterface $mailer;

    protected Context $context;

    /** @param EntityRepository<CustomerCollection> $customerRepository */
    public function __construct(EntityRepository $customerRepository, Context $context)
    {
        $this->customerRepository = $customerRepository;
        $this->context = $context;
    }

    public function createAndSaveCode(string $customerEmail): string
    {
        $code = $this->createCode();

        $customer = $this->getCustomerByEmail($customerEmail);

        if (null === $customer) {
            throw new CustomerNotFoundException($customerEmail);
        }

        $split = str_split((string) $code, 3);

        return implode('-', $split);
    }

    public function createCode(): ?int
    {
        return random_int(100000, 999999);
    }

    public function getCustomerByEmail(string $email): ?CustomerEntity
    {
        return $this->customerRepository->search((new Criteria())->addFilter(new EqualsFilter('email', $email)), $this->context)->first();
    }
}
