<?php

namespace App\Http\Responses\Concerns;

use App\Enums\UserRole;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait RedirectsToCurrentTeam
{
    protected function redirectPathForCurrentTeam(Request $request, string $redirect): string
    {
        $team = $this->currentTeam($request);

        URL::defaults(['current_team' => $team->slug]);

        return "/{$team->slug}{$this->dashboardPathForRole($request, $redirect)}";
    }

    /**
     * Admin diarahkan ke dashboard admin, klien ke dashboard biasa.
     */
    protected function dashboardPathForRole(Request $request, string $fallback): string
    {
        if ($request->user()?->role === UserRole::Admin) {
            return '/admin/dashboard';
        }

        return $fallback;
    }

    protected function currentTeam(Request $request): Team
    {
        $user = $request->user();

        abort_if(! $user, 403);

        $team = $user->currentTeam ?? $user->personalTeam();

        abort_if(! $team, 403);

        return $team;
    }
}
