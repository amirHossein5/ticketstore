<nav class="bg-gray-900 p-4">
    <section class="container mx-auto px-8 flex flex-row justify-between">
        <a href="/dashboard" class="uppercase font-bold text-base text-gray-200">ticketstore</a>

        <div class="flex gap-x-10 text-gray-400">
            <a
                href="{{ route('dashboard.tickets.create') }}"
                @if(request()->routeIs('dashboard.tickets.create'))
                    class="text-gray-200 font-semibold"
                @endif
            >
                Add ticket
            </a>

            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button>Log out</button>
            </form>
        </div>
    </section>
</nav>
