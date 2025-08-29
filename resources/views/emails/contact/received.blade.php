<x-mail::message>
    @component('mail::message')
    # New Contact Message

    **Name:** {{ $msg->name }}
    **Email:** {{ $msg->email }}
    **Subject:** {{ $msg->subject ?: '—' }}

    @component('mail::panel')
    {{ $msg->message }}
    @endcomponent

    _IP:_ {{ $msg->ip }}
    _User Agent:_ {{ Str::limit($msg->user_agent, 120) }}

    Thanks,
    {{ config('app.name') }}
    @endcomponent


    <x-mail::button :url="''">
        Button Text
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>