<x-app-layout>
    <x-slot:title>ticketstore</x-slot>

    <section class="flex items-center flex-col gap-5 my-20">
        @forelse($tickets as $ticket)
            <div class="flex flex-row w-[800px] relative">
                @if($ticket->sold_out)
                    <div class="absolute bg-gray-400 text-sm text-gray-900 right-0 top-0 rounded-tr-md p-1">
                        sold out
                    </div>
                @endif

                @if($ticket->image)
                    <div class="aspect-[4/3]">
                        <img
                            src="{{ asset($ticket->image)}}"
                            alt="ticket image"
                            class="max-w-[400px] max-h-[300px] rounded-l-md"
                        >
                    </div>
                @endif

                <div class="bg-[#2D2D35] flex flex-col justify-around w-full rounded-r-md pl-14 py-8">
                    <div>
                        <h1 class="text-gray-200 text-3xl">{{ $ticket->title }}</h1>
                        <p class="text-gray-400 text-sm mt-2">{{ $ticket->subtitle }}</p>
                        <p class="text-gray-200 text-sm">{{ $ticket->formatted_time_to_use }}</p>
                    </div>

                    <div class="flex flex-row gap-3 items-center mt-3">
                        <div class="text-gray-200">${{ $ticket->formatted_price }}</div>

                        @if (!$ticket->sold_out)
                            <a href="{{ route('purchase', $ticket) }}" class="text-blue-400 underline">Buy</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-200"> No tickets found! </p>
        @endforelse
    </section>
</x-app-layout>
