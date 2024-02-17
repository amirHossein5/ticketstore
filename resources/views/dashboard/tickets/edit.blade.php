<x-dashboard-layout>
    <x-slot name="title">edit ticket</x-slot>

    <section class="flex justify-center">
        <form class="flex flex-col gap-5 max-w-[500px] w-full" action="{{ route('dashboard.tickets.update', $ticket)}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')

            <div>
                <x-label for="title">Title</x-label>
                <x-text-input name="title" class="w-full" value="{{ old('title', $ticket->title) }}"/>
                <x-input-error :messages="$errors->get('title')"/>
            </div>

            <div>
                <x-label for="subtitle">subtitle</x-label>
                <x-text-input name="subtitle" class="w-full" value="{{ old('subtitle', $ticket->subtitle) }}"/>
                <x-input-error :messages="$errors->get('subtitle')"/>
            </div>

            <div>
                <x-label for="price">price</x-label>
                <div class="flex">
                    <span class="flex justify-center items-center p-2">$</span>
                    <x-text-input name="price" class="w-full" value="{{ old('price', $ticket->formatted_price) }}" placeholder="0.00"/>
                </div>
                <x-input-error :messages="$errors->get('price')"/>
            </div>

            <div>
                <x-label for="quantity">quantity</x-label>
                <x-text-input type="number" min="1" name="quantity" class="w-full" value="{{ old('quantity', $ticket->quantity) }}"/>
                <x-input-error :messages="$errors->get('quantity')"/>
            </div>

            <div>
                <x-label for="time_to_use">When to use tickets</x-label>
                <x-text-input type="datetime-local" name="time_to_use" class="w-full" value="{{ old('time_to_use', $ticket->time_to_use) }}"/>
                <x-input-error :messages="$errors->get('time_to_use')"/>
            </div>

            <div>
                @if($ticket->image)
                    <div class="aspect-[5/3] w-[400px]">
                        <img src="{{ asset($ticket->image) }}" alt="">
                    </div>
                @endif
                <x-label for="image">image (min width: 400px, ratio: 5/3)</x-label>
                <x-text-input type="file" name="image" class="w-full p-2"/>
                <x-input-error :messages="$errors->get('image')"/>
            </div>

            <div class="flex justify-center my-2">
                <x-primary-button class="hover:!bg-gray-300 !bg-gray-200 !text-black">Edit Ticket</x-primary-button>
            </div>
        </form>
    </section>

</x-dashboard-layout>
