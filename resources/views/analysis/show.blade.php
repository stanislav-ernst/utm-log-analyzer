<x-layout>
    <div class="text-base leading-[20px] flex-1 p-6 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
        <h1 class="text-xl mb-5">Analyse vom {{ $run->created_at->format('d.m.Y H:i') }}</h1>
        <p class="mb-1">Quelle: {{ $run->source_file }}</p>
        <p class="mb-1">Verarbeitete Zeilen: {{ $run->parsed_lines }}</p>
        <p class="mb-5">Fehlerhafte Zeilen: {{ $run->error_lines }}</p>
        @if($run->error_lines > 0)
            <div role="alert" class="mt-3 mb-5 relative flex w-full p-3 text-sm text-white bg-slate-800 rounded-md">
                <svg class="h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path fill="#e8d7e8" d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM224 160a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm-8 64l48 0c13.3 0 24 10.7 24 24l0 88 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-80 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l24 0 0-64-24 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                </svg>
                Bei der Auswertung wurden fehlerhafte Zeilen ignoriert.
            </div>
        @endif
        <h2 class="text-xl mb-2">Top Lizenzen</h2>
        <div class="relative flex flex-col w-full overflow-scroll text-slate-300 bg-slate-800 shadow-md rounded-lg bg-clip-border mb-5">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                <tr>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Seriennummer</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Anzahl</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($results[App\Enums\ResultType::LICENSE_ACCESS->value]->payload['items'] ?? [] as $item)
                    <tr class="even:bg-slate-900 hover:bg-slate-700">
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['serial'] }}</p>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['count'] }}</p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <h2 class="text-xl mb-2">Lizenz-Mehrfachnutzung</h2>
        <div class="relative flex flex-col w-full overflow-scroll text-slate-300 bg-slate-800 shadow-md rounded-lg bg-clip-border mb-5">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                <tr>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Seriennummer</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Geräte</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($results[App\Enums\ResultType::MULTI_DEVICE->value]->payload['items'] ?? [] as $item)
                    <tr class="even:bg-slate-900 hover:bg-slate-700">
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['serial'] }}</p>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['devices'] }}</p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <h2 class="text-xl mb-2">Hardware-Klassen</h2>
        <div class="relative flex flex-col w-full overflow-scroll text-slate-300 bg-slate-800 shadow-md rounded-lg bg-clip-border mb-5">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                <tr>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Seriennummer</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Geräte</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($results[App\Enums\ResultType::HARDWARE_CLASS->value]->payload['items'] ?? [] as $item)
                    <tr class="even:bg-slate-900 hover:bg-slate-700">
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['hardware_class'] }}</p>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $item['licenses'] }}</p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
