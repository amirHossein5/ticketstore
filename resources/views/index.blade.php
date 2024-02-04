<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ticketstore</title>

    @vite(['resources/css/app.css'])
</head>
<body class='bg-[#26262D]'>
    <section class="flex items-center flex-col gap-5 mt-20">
        @foreach(range(1,4) as $i)
            <div class="flex flex-row w-[800px]">
                <img src="{{ asset("images/image{$i}.png")}}" alt="ticket image" class="rounded-l-md">

                <div class="bg-[#2D2D35] flex flex-col justify-between w-full rounded-r-md pl-14 py-8">
                    <div>
                        <h1 class="text-gray-200 text-3xl">T20 South Africa vs Australia</h1>
                        <p class="text-gray-400 text-sm mt-1">SuperSport Park Guatang - South Africa</p>
                        <p class="text-gray-200 text-sm">Sun Feb 03 11:00AM</p>
                    </div>

                    <form class="flex flex-row gap-3 items-center" action="{{ route('purchase', $i)}}">
                        <input type="text" default="1" name="quantity" class="bg-[#43434E] rounded w-14 h-8 border-none text-gray-200 placeholder:text-center" placeholder="1">
                        <div class="text-gray-200">X</div>
                        <div class="text-gray-200">$25.00</div>
                        <button class="bg-[#076074] rounded w-14 h-8 flex justify-center items-center text-gray-200">Buy</button>
                    </form>
                </div>
            </div>
        @endforeach
    </section>
</body>
</html>
