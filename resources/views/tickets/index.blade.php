<x-app-layout>
    <x-slot:title>ticketstore</x-slot>

    <section class="flex items-center flex-col gap-5 mt-20">
        @forelse($tickets as $ticket)
            <div class="flex flex-row w-[800px]">
                @if($ticket->image)
                    <img src="{{ asset($ticket->image)}}" alt="ticket image" class="rounded-l-md">
                @endif

                <div class="bg-[#2D2D35] flex flex-col justify-between w-full rounded-r-md pl-14 py-8">
                    <div>
                        <h1 class="text-gray-200 text-3xl">{{ $ticket->title }}</h1>
                        <p class="text-gray-400 text-sm mt-1">{{ $ticket->subtitle }}</p>
                        <p class="text-gray-200 text-sm">{{ $ticket->formatted_time_to_use }}</p>
                    </div>

                    <form class="flex flex-row gap-3 items-center" action="{{ route('purchase', $ticket->ulid) }}">
                        <x-text-input value="1" name="quantity" class="w-14 h-8 text-center"/>
                        <div class="text-gray-200">X</div>
                        <div class="text-gray-200">{{ $ticket->formatted_price }}</div>
                        <button class="bg-[#076074] rounded w-14 h-8 flex justify-center items-center text-gray-200">Buy</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-200"> No tickets found! </p>
        @endforelse
    </section>
</x-app-layout>
