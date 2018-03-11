<?php
namespace App\Security;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $ri)
    {
        $this->router = $ri;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        #Redirecting to homepage via route name
        $url = $this->router->generate('homepage');
        return new RedirectResponse($url);
    }
}