<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set active application locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawLocale = Session::get('locale', config('app.locale', 'english'));

        // Normalize locale to our custom folder names
        if (in_array(strtolower($rawLocale), ['kh', 'km', 'khmer', 'khmer_unicode'])) {
            $locale = 'khmer_unicode';
        } else {
            $locale = 'english';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
