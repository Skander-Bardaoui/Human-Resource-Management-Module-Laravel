<?php

namespace App\Http\Middleware;

use App\Support\CollaboratorContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveCollaborator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $employee = CollaboratorContext::forUser($user);

        if ($employee === null) {
            if ($user?->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()
                ->route('profile.edit')
                ->with('error', 'Votre compte n\'est pas encore relié à une fiche collaborateur. Contactez les RH.');
        }

        $request->attributes->set('employee', $employee);
        $notifications = CollaboratorContext::notifications($employee);

        View::share([
            'currentEmployee' => $employee,
            'collaboratorNotifications' => $notifications,
            'collaboratorNotificationCount' => count($notifications),
        ]);

        return $next($request);
    }
}
