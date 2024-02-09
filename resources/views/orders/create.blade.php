<x-app-layout>
    <x-slot:title>payment</x-slot>

    <section class="flex justify-center">
        <div class="flex flex-col gap-y-5 mt-20 bg-[#2D2D35] px-10 py-8 rounded flex-wrap max-w-96">
            <div>
                <h1 class="uppercase font-bold text-base text-center text-gray-200">ticketstore</h1>
                <p class="text-gray-200 text-base mt-2 text-center">
                    <span class="text-gray-500">TICKETS TO</span> {{ $ticket->title }}
                </p>
            </div>

            <form
                class="flex flex-col gap-2"
                action="{{ route('order.store', $ticket) }}"
                method="post"
                x-data="{
                    price: @js($ticket->price),
                    quantity: @js(old('quantity', 1)),
                    number_format(number) {
                        return new Intl.NumberFormat('en-US', {
                          style: 'currency',
                          currency: 'USD',
                        }).format(number)
                    }
                }"
            >
                @csrf

                @if($errors->any())
                    <ul class="text-red-500 py-4">
                        @foreach($errors->all() as $error)
                            <li class="list-disc list-inside">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <div>
                    <x-label for="email">Email</x-label>
                    <x-text-input name="email" class="w-full" value="{{ old('email') }}"/>
                </div>

                <div>
                    <x-label for="quantity">Quantity</x-label>
                    <x-text-input
                        class="w-full"
                        type="number"
                        name="quantity"
                        x-model:value="quantity"
                        min="1"
                        max="{{ $ticket->quantity }}"
                    />
                </div>

                <div>
                    <x-label for="card_number">Card Number</x-label>
                    <x-text-input name="card_number" class="w-full" value="{{ old('card_number') }}"/>
                </div>

                <div class="flex flex-row justify-between gap-4">
                    <div class="flex flex-row w-1/2">
                        <div>
                            <x-label for="exp_month">Month</x-label>
                            <x-text-input
                                class="w-full rounded-r-none !border-solid border-transparent border-0 border-r-[1px] border-r-gray-500"
                                name="exp_month"
                                value="{{ old('exp_month') }}"
                            />
                        </div>
                        <div>
                            <x-label for="exp_year">Year</x-label>
                            <x-text-input name="exp_year" class="w-full rounded-l-none" value="{{ old('exp_year') }}"/>
                        </div>
                    </div>

                    <div class="w-1/2">
                        <x-label for="cvc">CVC</x-label>
                        <x-text-input name="cvc" class="w-full" value="{{ old('cvc') }}"/>
                    </div>
                </div>

                <div class="flex justify-center mt-2">
                    <button class="bg-[#076074] rounded w-40 h-10 flex justify-center items-center text-gray-200">
                        Pay
                        <span
                            class="ml-2"
                            x-text="number_format((price*quantity)/100)"
                        ></span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
