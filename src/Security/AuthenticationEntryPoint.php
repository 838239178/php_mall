<?php


namespace App\Security;


use App\Util\HttpUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private HttpUtils $httpUtils;

    public function __construct(UrlGeneratorInterface $urlGenerator, HttpUtils $httpUtils)
    {
        $this->urlGenerator = $urlGenerator;
        $this->httpUtils = $httpUtils;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if ($request->getPathInfo() === "/admin") {
            return new RedirectResponse($this->urlGenerator->generate("login"));
        }
        return $this->httpUtils->wrapperFail("unauthorized", Response::HTTP_UNAUTHORIZED);
    }
}