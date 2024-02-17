<x-dashboard-layout>
    <x-slot name="title">dashboard</x-slot>

    <x-slot name="secondNavigation">
        @if (session()->has('message'))
            <div class="container mx-auto mt-6" x-data="{ show: true }" x-show="show">
                <div class="border border-gray-500 rounded py-3 px-6 text-white flex justify-between items-center">
                    <span>{{ session('message') }}</span>

                    <span x-on:click="show = false" class="text-2xl p-2 cursor-pointer">x</span>
                </div>
            </div>
        @endif
    </x-slot>

    <section>
        <h1 class="text-xl border-b">Published</h1>

        <div class="flex gap-3 mt-5">
            @forelse($published as $ticket)
                <div class="flex gap-3">
                    <div class="py-6 px-8 min-w-60 bg-gray-900 rounded-md">
                        <h1 class="text-gray-200 text-xl">{{ $ticket->title }}</h1>
                        <p class="text-gray-400 text-sm mt-2">{{ $ticket->subtitle }}</p>
                        <p class="text-gray-200 text-sm">{{ $ticket->formatted_time_to_use }}</p>
                        <div class="text-gray-200">${{ $ticket->formatted_price }}</div>

                        <div class="flex gap-4 items-center mt-6">
                            <a href="{{ route('dashboard.published_tickets.orders', $ticket) }}" class="bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest">Manage</a>
                        </div>
                    </div>
                </div>
            @empty
                <span class="text-gray-300">No published tickets</span>
            @endforelse
        </div>
    </section>

    <section class="mt-10">
        <h1 class="text-xl border-b">Draft</h1>

        <div class="flex gap-3 mt-5">
            @forelse($drafts as $ticket)
                <div class="flex gap-3">
                    <div class="py-6 px-8 min-w-60 bg-gray-900 rounded-md">
                        <h1 class="text-gray-200 text-xl">{{ $ticket->title }}</h1>
                        <p class="text-gray-400 text-sm mt-2">{{ $ticket->subtitle }}</p>
                        <p class="text-gray-200 text-sm">{{ $ticket->formatted_time_to_use }}</p>
                        <div class="text-gray-200">${{ $ticket->formatted_price }}</div>

                        <div class="flex gap-4 items-center mt-6">
                            <a href="{{ route('dashboard.tickets.edit', $ticket) }}" class="bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest">Edit</a>

                            <form action="{{ route('dashboard.published_tickets.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="ticket" value="{{ $ticket->ulid }}">
                                <x-primary-button class="!bg-blue-600 hover:!bg-blue-700 text-black">Publish</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <span class="text-gray-300">No draft tickets</span>
            @endforelse
        </div>
    </section>
</x-dashboard-layout>
