<x-app-layout>
    <x-slot:title>your order</x-slot>

    <section class="container md:px-20 mx-auto mt-7">
        <div class="border-b print:border-black pb-2 flex flex-row justify-between items-center">
            <h1 class="text-gray-200 text-3xl">Order Summary</h1>
            <a href="" class="text-base print:text-black text-blue-500">EFKLSJFS23442LSKDJFS2323</a>
        </div>

        <div class="flex flex-row gap-2 text-xl text-gray-200 mt-4">
            <p>Order Total:</p>
            <p>$84.00</p>
        </div>
        <div class="flex flex-row gap-2 text-xl text-gray-200 mt-2">
            <p>Card Number:</p>
            <p>**** **** **** 4242</p>
        </div>

        <div class="border-b print:border-black mt-10 pb-2">
            <h1 class="text-gray-200 text-2xl">Your Tickets</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            @foreach(range(1,4) as $i)
                <div>
                    <div class="bg-gray-900 p-3 rounded-t-md print:border print:border-black">
                        <div class="flex flex-row justify-between">
                            <h1 class="text-gray-200 text-xl md:text-2xl">T20 South Africa vs Australia</h1>
                            <p class="text-gray-200 md:text-base text-sm">Sun Feb 03 11:00AM</p>
                        </div>
                        <p class="mt-2 text-gray-200 md:text-base text-sm">SuperSport Park Guatang - South Africa</p>
                    </div>

                    <div class="flex flex-row justify-between text-gray-200 bg-gray-800 border-t-0 print:border-t border border-gray-900 rounded-b-md p-3">
                        <p class="text-xl">KAEJBS</p>
                        <p>example@gmail.com</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-app-layout>
