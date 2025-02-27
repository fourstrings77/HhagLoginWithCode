<?php

namespace HhagLoginWithCode\Storefront\Controller;

use HhagLoginWithCode\Service\LoginWithCodeService;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Account\Login\AccountLoginPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginWithCodeController extends StorefrontController
{
    protected AccountLoginPageLoader $accountLoginPageLoader;
    protected EntityRepository $customerEntity;
    public function __construct(AccountLoginPageLoader $accountLoginPageLoader, EntityRepository $customerEntity)
    {
        $this->accountLoginPageLoader = $accountLoginPageLoader;
        $this->customerEntity = $customerEntity;
    }

    #[Route(path: '/request-code', name: 'frontend.account.login.request_code', defaults: ['_routeScope' => ['storefront']], methods: ['GET'])]
    public function requestCodeForm(Request $request, SalesChannelContext $context) : Response{

        $page = $this->accountLoginPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/page/account/login/request-code.html.twig', [
            'redirectTo' => '',
            'page' => $page
        ]);
    }

    #[Route(path: '/verify-code', name: 'frontend.account.login.verify_code', defaults: ['_routeScope' => ['storefront']], methods: ['POST'])]
    public function verfify(Request $request, SalesChannelContext $context) : Response{

        $redirect = $request->get('redirectTo', 'frontend.account.home.page');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $request->get('username')));

        $customer = $this->customerEntity->search($criteria, $context->getContext())->first();

        if(!$customer){
            $this->addFlash(parent::DANGER, 'du kommsch hier ned rein!');
           return $this->redirectToRoute('frontend.account.login.request_code');
        }

        $lwcService = new LoginWithCodeService($this->customerEntity, $context->getContext());
        $code = $lwcService->createAndSaveCode($customer->getEmail());


    }
}