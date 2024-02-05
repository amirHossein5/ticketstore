<x-app-layout>
    <x-slot:title>payment</x-slot>

    <section class="flex justify-center">
        <div class="flex flex-col gap-y-8 mt-20 bg-[#2D2D35] px-10 py-8 rounded">
            <div>
                <h1 class="uppercase font-bold text-base text-center text-gray-200">ticketstore</h1>
                <p class="text-gray-200 text-base mt-2">{{ $quantity }} tickets to {{ $ticket->title }}</p>
            </div>

            <form class="flex flex-col gap-2">
                <div>
                    <x-label for="email">Email</x-label>
                    <x-text-input name="email" class="w-full"/>
                </div>

                <div>
                    <x-label for="card_number">Card Number</x-label>
                    <x-text-input name="card_number" class="w-full"/>
                </div>

                <div class="flex flex-row justify-between gap-4">
                    <div class="flex flex-row">
                        <div>
                            <x-label for="exp_month">Month</x-label>
                            <x-text-input name="exp_month" class="w-20 rounded-r-none !border-solid border-transparent border-0 border-r-[1px] border-r-gray-500"/>
                        </div>
                        <div>
                            <x-label for="exp_year">Year</x-label>
                            <x-text-input name="exp_year" class="w-20 rounded-l-none"/>
                        </div>
                    </div>
                    <div>
                        <x-label for="CVC">CVC</x-label>
                        <x-text-input name="CVC" class="max-w-28"/>
                    </div>
                </div>

                <div class="flex justify-center mt-2">
                    <button class="bg-[#076074] rounded w-40 h-10 flex justify-center items-center text-gray-200">
                        Pay $47.00
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
