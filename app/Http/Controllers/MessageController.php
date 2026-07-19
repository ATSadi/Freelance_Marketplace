<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $projects = Project::query()
            ->whereNotNull('freelancer_id')
            ->when($user->role !== User::ROLE_ADMIN, fn ($query) => $query
                ->where(fn ($participants) => $participants
                    ->where('client_id', $user->id)
                    ->orWhere('freelancer_id', $user->id)))
            ->with(['client.profile', 'freelancer.profile', 'latestMessage.sender'])
            ->withMax('messages', 'created_at')
            ->withCount(['messages as unread_messages_count' => fn ($query) => $query
                ->whereNull('read_at')
                ->where('sender_id', '!=', $user->id)])
            ->orderByDesc('messages_max_created_at')
            ->get();

        return view('messages.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        $this->ensureParticipant($project);

        $project->load(['client', 'freelancer', 'messages.sender']);
        $project->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('project'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->ensureParticipant($project, allowAdmin: false);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = $project->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        $recipient = Auth::id() === $project->client_id ? $project->freelancer : $project->client;
        $recipient?->notify(new NewMessageNotification($message));

        return redirect()->route('messages.show', $project);
    }

    private function ensureParticipant(Project $project, bool $allowAdmin = true): void
    {
        $user = Auth::user();
        $isParticipant = in_array($user->id, [$project->client_id, $project->freelancer_id], true);

        abort_unless($isParticipant || ($allowAdmin && $user->role === User::ROLE_ADMIN), 403);
    }
}
