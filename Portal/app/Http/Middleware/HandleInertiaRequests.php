<?php

namespace App\Http\Middleware;

use App\Models\FriendRequest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Lazily...
            'auth.user' => fn() => $request->user()
                ? $request->user()->only('id', 'name', 'profilepic', 'timezone')
                : null,
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
            'notificationNumber' => fn() => $request->user() ? Invitation::where('status', 'pending')->where('receiver_id', $request->user()->id)
                ->count() + FriendRequest::where('status', 'pending')->where('receiver_id', $request->user()->id)->count() : 0,
            'userId' => fn() => $request->user() ? Auth::Id() : -1,
        ]);
    }


}
