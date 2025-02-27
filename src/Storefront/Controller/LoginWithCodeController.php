<?php

namespace HhagLoginWithCode\Storefront\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Account\Login\AccountLoginPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginWithCodeController extends StorefrontController
{
    protected AccountLoginPageLoader $accountLoginPageLoader;
    public function __construct(AccountLoginPageLoader $accountLoginPageLoader)
    {
        $this->accountLoginPageLoader = $accountLoginPageLoader;
    }

    #[Route(path: '/request-code', name: 'frontend.account.login.request_code', defaults: ['_routeScope' => ['storefront']], methods: ['GET'])]
    public function requestCodeForm(Request $request, SalesChannelContext $context) : Response{

        $page = $this->accountLoginPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/page/account/login/request-code.html.twig', [
            'redirectTo' => '',
            'page' => $page
        ]);
    }

    #[Route(path: '/verify-code', name: 'frontend.account.login.verify_code', defaults: ['_routeScope' => ['storefront']], methods: ['GET'])]
    public function verfify() : Response{
        dd('test');
    }
}