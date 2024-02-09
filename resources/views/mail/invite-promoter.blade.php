<p>Ticketstore registration link: </p>

<a href="{{ $invitation->register_link }}">{{ $invitation->register_link }}</a>

<p>Link will expire at {{ $invitation->created_at->addMinutes(30)->format('H:i') }}</p>
