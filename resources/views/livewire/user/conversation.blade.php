<?php

namespace App\Livewire\User;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new #[Layout('components.layouts.user-app')] class extends Component
{
    use WithFileUploads;

    public Conversation $conversation;

    // Stocke les messages sous forme de tableau simple
    public array $messages = [];

    #[Rule(['required', 'string', 'max:2000'])]
    public string $messageText = '';

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation->load(['participants.user']);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = $this->conversation
            ->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        $this->validate();

        $message = $this->conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $this->messageText,
            'type' => 'text',
        ]);

        $this->reset('messageText');
        $this->loadMessages();

        $this->conversation->update(['last_message_id' => $message->id]);
    }


};?>

<div wire:poll.10s class="w-full p-1 h-screen bg-gray-100 dark:bg-gray-900 box-border">
    <div class="mt-6 sm:px-10 sm:w-[70%] w-[100%]">
        <div class="bg-white dark:bg-gray-800 h-[calc(100vh-theme(spacing.16))] rounded-lg relative flex flex-col shadow-lg">
            {{-- Conversation Header --}}
            <div class="p-4 border-gray-200 dark:border-gray-700 border-b-2 flex items-center">
                @php
                    $otherParticipant = collect($conversation->participants)->first(function($participant) {
                        return $participant['user_id'] !== \Illuminate\Support\Facades\Auth::id();
                    });
                    $otherUser = $otherParticipant['user'] ?? null;
                @endphp
                <div class="relative"> {{-- Added relative positioning for the online indicator --}}
                    <a href="{{ route('users.account.view', $otherUser['id']) }}">
                        <div>
                            {{-- Check if a profile photo URL exists --}}
                            @if (!empty($otherUser['profile'])) {{-- Changed from $user->profile to $user->profile --}}
                                <img
                                    class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                    src="{{ Storage::url($otherUser['profile']) }}"
                                    alt="{{ $otherUser['name'] }} Avatar"
                                />
                            @else
                                {{-- Fallback: Display initials if no profile photo --}}
                                <div
                                    class="flex items-center justify-center border rounded-full w-[50px] h-[50px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                    title="{{ $otherUser['name']}}"
                                >
                                    @php
                                        $nameParts = explode(' ', $otherUser['name']);
                                        $initials = '';
                                        if (count($nameParts) > 0) {
                                            $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                        }
                                        if (count($nameParts) > 1) {
                                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                        } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                            $initials = strtoupper(substr($nameParts[0], 0, 2));
                                        }
                                        if (empty($initials) && !empty($otherUser['name'])) {
                                            $initials = strtoupper(substr($otherUser['name'], 0, 1));
                                        } elseif (empty($initials)) {
                                            $initials = '?';
                                        }
                                    @endphp
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    </a>
                    {{-- Online indicator dot --}}
                    <div class="w-3 h-3 rounded-full bg-green-500 absolute bottom-0 right-4 border-white border-2 dark:border-gray-800"></div>
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $otherUser['name'] ?? 'Unknown User' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Online</p>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-grow overflow-y-auto p-4 space-y-4 no-scrollbar" x-data="{
                init() {
                    this.$nextTick(() => {
                        this.$el.scrollTop = this.$el.scrollHeight;
                    });
                },
                messagesUpdated() {
                    this.$nextTick(() => {
                        this.$el.scrollTop = this.$el.scrollHeight;
                    });
                }
            }" x-on:livewire:updated="messagesUpdated">
                @foreach ($messages as $message)
                    @php
                        $isSender = $message['sender_id'] === \Illuminate\Support\Facades\Auth::id();
                    @endphp
                    @if ($isSender)
                        <div class="flex justify-end">
                            <div class="bg-orange-500 text-white p-3 rounded-lg max-w-[75%] shadow-md">
                                {{ $message['body'] }}
                                <div class="flex items-center justify-end text-xs text-orange-200 mt-1">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                    {{-- Read receipt icon (mdi-check-all for read, mdi-check for sent) --}}
                                    {{-- For a real 'read' status, you'd need a 'read' property on the message --}}
                                    <i class="mdi mdi-check-all ml-1 text-base"></i> {{-- Or mdi-check for sent, mdi-check-all for read --}}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start">
                            <div class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-3 rounded-lg max-w-[75%] shadow-md">
                                {{ $message['body'] }}
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Message Input Area --}}
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form wire:submit.prevent="sendMessage">
                    <div class="flex items-center space-x-3">
                        <textarea
                            wire:model="messageText"
                            class="flex-grow border border-gray-300 dark:border-gray-600 rounded-full h-10 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 resize-none p-2 focus:outline-none focus:border-orange-500 dark:focus:border-orange-500 focus:ring-0 no-scrollbar placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Type your message..."
                            rows="1"
                        ></textarea>
                        <button
                            type="submit"
                            class="flex-shrink-0 flex items-center justify-center p-3 bg-orange-600 rounded-full text-white hover:bg-orange-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-5 h-5 -rotate-45 transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            <span class="sr-only">Send Message</span>
                        </button>
                    </div>
                    @error('messageText') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </form>
            </div>
        </div>
    </div>
    {{-- Right Sidebar --}}
    <div class="fixed right-0 top-0 w-[25%] py-4 px-6 box-border bg-white dark:bg-gray-800 h-screen shadow-md sm:block hidden z-40">
        <livewire:user.list-message />
    </div>
</div>