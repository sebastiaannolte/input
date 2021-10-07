<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class DomainCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowedHosts = explode(',', env('ALLOWED_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
        dd($request->ip());
        // dd($requestHost);
        if(!app()->runningUnitTests()) {
            if(!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];
                // dd($requestInfo);
                // event(new UnauthorizedAccess($requestInfo));

                throw new Exception('This host is not allowed');
            }
        }

        return $next($request);
    }
}
