<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/18
 * Time: 上午1:27
 */

namespace App\Http\Middleware;

use Closure;

class ExampleMiddleware
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
        return $next($request);
    }
}
