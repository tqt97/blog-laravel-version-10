<nav class="w-full py-4 bg-blue-800 shadow">
    <div class="w-full container mx-auto flex flex-wrap items-center justify-between">
        <nav>
            <ul class="flex items-center justify-between font-bold text-sm text-white uppercase no-underline">
                @foreach ($mainMenu->items as $item)
                    <li>
                        <a class="hover:text-gray-200 hover:underline px-4" href="{{ url($item['data']['url']) }}"
                            @if ($item['type'] === 'external-link') target="{{ $item['data']['target'] }}" @endif>
                            @isset($item['data']['title'])
                                {{ $item['data']['title'] }}
                            @else
                                {{ $item['label'] }}
                            @endisset

                            {{-- @isset($item['data']['description'])
                                <span class="block text-xs">{{ $item['data']['description'] }}</span>
                            @endisset --}}

                            @if ($item['type'] === 'external-link')
                                <x-heroicon-o-external-link class="inline-block w-4 h-4" />
                            @endif
                        </a>

                        @if ($item['children'])
                            <ul>
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <a class="hover:text-gray-200 hover:underline px-4"
                                            href="{{ url($item['data']['url'] . '/' . $child['data']['url']) }}">
                                            @isset($child['data']['title'])
                                                {{ $child['data']['title'] }}
                                            @else
                                                {{ $child['label'] }}
                                            @endisset
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

</nav>
