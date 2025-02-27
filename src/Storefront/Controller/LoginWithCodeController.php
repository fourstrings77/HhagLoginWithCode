<?php

namespace HhagLoginWithCode\Storefront\Controller;

use http\Params;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginWithCodeController extends StorefrontController
{
    #[Route(path: '/request-code', name: 'frontend.account.login.request_code', defaults: ['_routeScope' => ['storefront']], methods: ['GET'])]
    public function requestCodeForm() : Response{
        dd('test');
    }
}