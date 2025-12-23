<x-layout>
    <div class="text-base leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
        <p class="mb-5">Keine Ergebnisse gefunden. Führen Sie den artisan Befehl aus, um Daten zu generieren.</p>
        <div role="alert" class="mt-3 mb-5 relative flex w-full p-3 text-sm text-white bg-slate-800 rounded-md">
            Denken Sie daran, die Datei in das entsprechende Verzeichnis zu kopieren und den Artisan-Befehlspfad anzupassen.
        </div>
    </div>
    <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:py-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-tr-lg rounded-tl-lg lg:rounded-br-lg lg:rounded-tl-none">
        <code class="text-sm sm:text-base inline-flex text-left items-center space-x-4 bg-slate-800 text-white rounded-lg p-4 pl-6">
            <span class="flex gap-4">
                <span class="shrink-0 text-gray-500">
                    $
                </span>
                <span class="flex-1">
                    <span>
                        php artisan utm:analyze
                    </span>
                    <span class="text-yellow-500">
                        storage/app/private/utm-logs/[filename].log
                    </span>
                </span>
            </span>
        </code>
    </div>
</x-layout>
