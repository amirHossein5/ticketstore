<x-dashboard-layout>
    <x-slot name="title">The {{ $ticket->title }} Orders</x-slot>

    <x-slot name="secondNavigation">
        <section class="bg-gray-700">
            <div class="container mx-auto px-8 py-4 flex justify-between">
                <div class="flex gap-x-3">
                    <h1 class="font-semibold text-gray-200 text-xl">{{ $ticket->title }}</h1>
                    <span class="text-gray-400 text-xl">/</span>
                    <p class="text-gray-300">{{ $ticket->formatted_time_to_use }}</p>
                </div>

                <div>
                    <a href="{{ route('dashboard.published_tickets.attendee_message.create', $ticket) }}" class="text-gray-800 uppercase text-sm tracking-wider font-semibold rounded-md border border-transparent bg-white py-2 px-3">Send message to attendees</a>
                </div>
            </div>
        </section>
    </x-slot>

    <section>
        <h1 class="text-xl">Overview</h1>

        <div class="bg-gray-700 rounded-md mt-4">
            <div class="flex flex-col border-b border-gray-600 px-6 py-8">
                <span>This ticket is {{ $soldOutPercentage }}% sold out.</span>

                <div class="h-4 rounded bg-gray-300 w-full mt-6">
                    <div class="h-full rounded-l bg-green-600" style="width: {{ $soldOutPercentage }}%"></div>
                </div>
            </div>

            <div class="flex">
                <div class="p-6 w-1/3 border-r border-gray-600">
                    <p class="capitalize text-lg">Total Tickets Remaining</p>
                    <p class="text-4xl mt-4">{{ $ticket->quantity }}</p>
                </div>
                <div class="p-6 w-1/3 border-r border-gray-600">
                    <p class="capitalize text-lg">Total Tickets Sold</p>
                    <p class="text-4xl mt-4">{{ $ticket->sold_count }}</p>
                </div>
                <div class="p-6 w-1/3 border-gray-600">
                    <p class="capitalize text-lg">Total Revenue</p>
                    <p class="text-4xl mt-4">${{ $totalRevenueInDollars }}</p>
                </div>
            </div>
        </div>

        <h1 class="text-xl mt-10">Recent Orders</h1>

        @if($orders->isEmpty())
            <span class="text-white">No Orders!</span>
        @else
            <div class="mt-3 mb-7">
                <table class="w-full">
                    <thead>
                        <tr class="border-gray-600 border-b">
                            <th class="text-left p-4">Email</th>
                            <th class="text-left p-4">Tickets</th>
                            <th class="text-left p-4">Amount</th>
                            <th class="text-left p-4">Card</th>
                            <th class="text-left p-4">Purchased At</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($orders as $order)
                            <tr class="last:border-b-0 border-b border-gray-700">
                                <td class="p-4">{{ $order->email }}</td>
                                <td class="p-4">{{ $order->tickets()->count() }}</td>
                                <td class="p-4">${{ $order->charged_in_dollars }}</td>
                                <td class="p-4">**** {{ $order->last_4 }}</td>
                                <td class="p-4">{{ $order->created_at->toDateTimeString() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-dashboard-layout>
