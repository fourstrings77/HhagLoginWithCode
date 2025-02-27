<?php declare(strict_types=1);

namespace HhagLoginWithCode\Service;

use Brick\Math\Exception\IntegerOverflowException;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class LoginWithCodeService
{
    protected EntityRepository $customerRepository;
    protected MailerInterface $mailer;
    protected Context $context;

    public function __construct(/*MailerInterface $mailer,*/ EntityRepository $customerRepository, Context $context){
            //$this->mailer = $mailer;
            $this->customerRepository = $customerRepository;
            $this->context = $context;
    }

    public function createAndSaveCode(string $customerEmail): int{

        $code = random_int(100000, 999999);

        $customer = $this->customerRepository->search((new Criteria())->addFilter(new EqualsFilter('email', $customerEmail)), $this->context)->first();

        if(!$customer){
            throw new CustomerNotFoundException($customerEmail);
        }

        $this->customerRepository->update([
            'id' => $customer->getId(),
            'extensions' => [[
                'login_code' => $code,
                ],
            ]
        ], $this->context);

        return $code;
    }


}
