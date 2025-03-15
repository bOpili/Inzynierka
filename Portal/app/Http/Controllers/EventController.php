<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Game;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {

        // dd($request);
        $events = Event::when($request->tags, function ($query) use ($request) {
            foreach ($request->tags as $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tags.id', $tag);
                });
            }
        })->when($request->games, function ($query) use ($request) {
            $query->whereIn('game_id', $request->games);
        })->when(
                $request->search,
                function ($query) use ($request) {
                    $query->where('title', 'like', "%" . $request->search . "%");
                }
            )->latest()->withCount(['users'])->paginate(3)->withQueryString();
        $events->load('game')->load('tags');
        return Inertia::render('Events/Events', [
            'events' => $events,
            'joinMessage' => session('joinMessage'),
            'tags' => Tag::get(),
            'games' => Game::get(),
            'searchValue' => $request->search ? $request->search : "",
            'filteredTags' => $request->tags,
            'filteredGames' => $request->games
        ]);
    }

    public function join(Request $request)
    {
        $user = User::find(Auth::id());
        $event = Event::findOrFail($request->eventId);

        if ($event->users->find(Auth::id())) {
            return back()->with('message', 'You already participate in this event');
        }


        if ($event->users->count() >= $event->slots) {
            return back()->with('message', 'No slots available');
        }

        $overlappingEvent = $user->events()
            ->where(function ($query) use ($event) {
                $query->where('startDate', '<', $event->endDate)
                    ->where('endDate', '>', $event->startDate);
            })
            ->first();

        if ($overlappingEvent) {
            return back()->with('message', "You are already participating in " . $overlappingEvent->title . " during this time.");
        }

        $event->users()->attach(Auth::id());

        return back()->with('message', 'Join request sent');
    }


    public function leave(Request $request)
    {
        $event = Event::findOrFail($request->eventId);

        $event->users()->detach(Auth::id());

        return back();
    }

    public function kick(Request $request)
    {
        $event = Event::findOrFail($request->eventId);

        $auth = Gate::inspect('kick', $event);

        if ($auth->allowed()) {
            $event->users()->detach($request->userId);
            return back()->with('message', 'User kicked');
        } else {
            return redirect()->back()->with(['message' => $auth->message()]);
        }
        ;
    }

    public function accept(Request $request)
    {
        $event = Event::findOrFail($request->eventId);

        if ($event->users()->withPivot('status')->where('status', '>', 0)->count() < $event->slots) {
            $event->users()->where('user_id', $request->userId)->update(['status' => 1]);
            return back()->with('message', 'User accepted');
        } else {
            $event->users()->where('user_id', $request->userId)->update(['status' => 0]);
            return back()->with('message', 'No more empty slots for this user');
        }
    }

    public function create()
    {
        return Inertia::render('Events/EventForm', ['tags' => Tag::get(), 'games' => Game::get(), 'timezone' => User::findOrFail(Auth::id())->timezone]);
    }

    public function store(StoreEventRequest $request)
    {
        $fields = $request->validate([
            "title" => ['required', 'max:255'],
            "description" => ['required', 'max:500'],
            "slots" => ['required', 'int'],
            "image" => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp'],
            "startDate" => ['required', 'date', 'after:now'],
            "endDate" => ['required', 'date', 'after:startDate'],
            "game_id" => ['required'],
            "ip" => ['required', 'ipv4'],
            "password" => ['required'],
        ]);


        if ($request->hasFile('image')) {
            $fields['image'] = Storage::disk('public')->put('Events', $request->image);
        } else {
            $fields['image'] = 'Events/DefaultEvent2.webp';
        }


        $event = $request->user()->events()->create($fields);

        if($request->tags){
            foreach ($request->tags as $tag) {

                $event->tags()->attach($tag['id']);
            }
        }

        $event->users()->where('user_id', Auth::id())->update(['status' => 2]);

        return redirect()->route('events');
    }

    public function show(Event $event)
    {
        $userStatus = $event->userStatus(Auth::id());

        $event = Event::withCount(
            [
                'users' => function ($query) {
                    $query->where('status', '>', 0);
                },
                'users as users_awaiting' => function ($query) {
                    $query->where('status', 0);
                }
            ]
        )->where('id', $event->id)->first();

        $event->load('game')->load('tags');

        $canSeeJoinFields = Gate::inspect('viewJoinCredentials', $event);

        if ($canSeeJoinFields->allowed()) {
            $event = $event->makeVisible(['ip', 'password']);
        }

        return Inertia::render(
            'Events/EventDetails',
            [
                'event' => $event,
                'users' => $event->users()->where('status', '>', 0)->select('users.id', 'profilepic', 'name')->orderBy('status', 'DESC')->get(),
                'userStatus' => $userStatus !== null ? $userStatus : -1,
                'pendingUsers' => $event->users()->where('status', '=', 0)->select('profilepic', 'name')->get(),
                'friends' => User::findOrFail(Auth::id())->friends()->select('users.id', 'profilepic', 'name')->get(),
            ]
        );
    }

    public function edit(Event $event)
    {
        //
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    public function destroy(Event $event)
    {

        $auth = Gate::inspect('delete', $event);

        if ($auth->allowed()) {
            Event::find($event->id)->delete();
            return redirect()->route('events');
        } else {
            return redirect()->back()->with(['message' => $auth->message()]);
        }

    }
}
