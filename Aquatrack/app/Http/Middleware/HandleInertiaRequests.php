<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'trackingCode' => fn() => $request->session()->get('trackingCode'),
                'dateSubmitted' => fn() => $request->session()->get('dateSubmitted'),
                'generated_password' => fn() => $request->session()->get('generated_password'),
                'logout_success' => $request->session()->get('logout_success'),
            ],
            'auth' => [
                'user' => $request->user() ? [
                    ...$request->user()->toArray(),
                    'avatar_url' => $request->user()->avatar_url
                ] : null,
            ],
        ]);
    }
}
