<x-layout>
    <div class="text-base leading-[20px] flex-1 p-6 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
        <h1 class="text-xl mb-5">Analysen</h1>
        <div class="relative flex flex-col w-full overflow-scroll text-slate-300 bg-slate-800 shadow-md rounded-lg bg-clip-border">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                <tr>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Datum</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Datei</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Zeilen</p>
                    </th>
                    <th class="p-4 border-b border-slate-600 bg-slate-700">
                        <p class="text-sm font-normal leading-none text-slate-300">Fehler</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($runs as $run)
                    <tr class="even:bg-slate-900 hover:bg-slate-700">
                        <td class="p-4 border-b border-slate-700">
                            <a class="text-sm text-slate-100 font-semibold" href="{{ route('analysis-runs.show', $run) }}">
                                {{ $run->created_at->format('d.m.Y H:i') }}
                            </a>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ basename($run->source_file) }}</p>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $run->parsed_lines }}</p>
                        </td>
                        <td class="p-4 border-b border-slate-700">
                            <p class="text-sm text-slate-300">{{ $run->error_lines }}</p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if ($runs->hasPages())
            <div class="mt-4">
                {{ $runs->links() }}
            </div>
        @endif
    </div>
</x-layout>
