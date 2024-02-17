<x-dashboard-layout>
    <x-slot name="title">Send message to attendees</x-slot>

    <x-slot name="secondNavigation">
        <section class="bg-gray-700">
            <div class="container mx-auto px-8 py-4 flex gap-x-3">
                <h1 class="font-semibold text-gray-200 text-xl">{{ $ticket->title }}</h1>
                <span class="text-gray-400 text-xl">/</span>
                <p class="text-gray-300">{{ $ticket->formatted_time_to_use }}</p>
            </div>
        </section>
    </x-slot>

    <section class="flex justify-center">
        <form action="{{ route('dashboard.published_tickets.attendee_message.store', $ticket) }}" method="post" class="flex flex-col gap-5 max-w-[700px] w-full">
            @csrf

            <div>
                <x-label for="title">Title</x-label>
                <x-text-input name="title" class="w-full" value="{{ old('title') }}"/>
                <x-input-error :messages="$errors->get('title')"/>
            </div>

            <div>
                <x-label for="body">body</x-label>
                <x-textarea name="body" class="w-full">{{ old('body') }}</x-textarea>
                <x-input-error :messages="$errors->get('body')"/>
            </div>

            <div class="flex justify-center my-2">
                <x-primary-button class="hover:!bg-gray-300 !bg-gray-200 !text-black">Create Ticket</x-primary-button>
            </div>
        </form>
        </form>
    </section>
</x-dashboard-layout>
