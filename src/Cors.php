<?php

namespace Spatie\CorsLite;

use Closure;
use Spatie\CorsLite\CorsProfile\CorsProfile;
use Illuminate\Http\Response;

class Cors
{
    /** \Spatie\CorsLite\CorsProfile\CorsProfile */
    protected $corsProfile;

    public function __construct(CorsProfile $corsProfile)
    {
        $this->corsProfile = $corsProfile;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->corsProfile->setRequest($request);

        if ($this->isPreflightRequest($request)) {
            return $this->handlePreflightRequest();
        }

        $response = $next($request);

        return $this->corsProfile->addCorsHeaders($response);
    }

    protected function isPreflightRequest($request): bool
    {
        return $request->getMethod() === "OPTIONS";
    }

    protected function handlePreflightRequest()
    {
        if (! $this->corsProfile->isAllowed()) {
            return response('Forbidden.', 403);
        }

        return $this->corsProfile->addPreflightheaders(response('Preflight OK', 200));
    }
}