<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

class SafeStartSession extends StartSession
{
    /**
     * Handle stateful requests without letting background polling or read-only
     * JSON requests age flash data or overwrite concurrent session writes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Closure  $next
     * @return mixed
     */
    protected function handleStatefulRequest(Request $request, $session, Closure $next)
    {
        if ($this->shouldPreserveFlashSession($request)) {
            $request->setLaravelSession(
                $this->startSession($request, $session)
            );

            return $next($request);
        }

        return parent::handleStatefulRequest($request, $session, $next);
    }

    /**
     * Determine whether the incoming request is a background/polling endpoint
     * that must only read session identity without consuming redirect flash state.
     */
    protected function shouldPreserveFlashSession(Request $request): bool
    {
        if ($request->is(
            'chat/unread-count',
            'notifications/unread',
            'chat/messages/*',
            'photographer/chat/messages/*',
            'chat/typing',
            'broadcasting/auth',
            'api/*'
        )) {
            return true;
        }

        return $request->isMethod('GET') && ($request->expectsJson() || $request->wantsJson());
    }
}

