<x-app-layout>
    <x-slot:title>your order</x-slot>

    <div class="bg-red-200 text-center print:hidden">
        This page expires in {{ $order->created_at->addMinutes(30)->format('H:i') }}
    </div>

    <section class="container md:px-20 mx-auto my-7">
        <div class="border-b print:border-black pb-2 flex flex-row justify-between items-center">
            <h1 class="text-gray-200 text-3xl">Order Summary</h1>
            <a href="{{ request()->fullUrl() }}" class="text-base print:text-black text-blue-500">{{ $order->code }}</a>
        </div>

        <div class="flex flex-row gap-2 text-xl text-gray-200 mt-4">
            <p>Order Total:</p>
            <p>${{ $order->formatted_charged }}</p>
        </div>
        <div class="flex flex-row gap-2 text-xl text-gray-200 mt-2">
            <p>Card Number:</p>
            <p>**** **** **** {{ $order->last_4 }}</p>
        </div>

        <div class="border-b print:border-black mt-10 pb-2">
            <h1 class="text-gray-200 text-2xl">Your Tickets</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            @foreach($order->tickets as $ticket)
                <div>
                    <div class="bg-gray-900 p-3 rounded-t-md print:border print:border-black">
                        <div class="flex flex-row justify-between">
                            <h1 class="text-gray-200 text-xl md:text-2xl">{{ $ticket->title }}</h1>
                            <p class="text-gray-200 md:text-base text-sm text-end">{{ $ticket->formatted_time_to_use }}</p>
                        </div>
                        <p class="mt-2 text-gray-200 md:text-base text-sm">{{ $ticket->subtitle }}</p>
                    </div>

                    <div class="flex flex-row justify-between text-gray-200 bg-gray-800 border-t-0 print:border-t border border-gray-900 rounded-b-md p-3">
                        <p class="text-xl">{{ $ticket->pivot->code }}</p>
                        <p>{{ $order->email }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-app-layout>
