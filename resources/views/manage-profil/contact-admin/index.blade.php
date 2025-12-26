@extends('layouts.app')

@section('title', 'Contact Admin')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Contact Admin</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Reach our administrators using the numbers below.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 px-6 pb-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($contacts as $contact)
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-navy-700">
                    <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-300">{{ $contact['label'] }}</p>
                    <p class="mt-1 text-lg font-bold text-navy-700 dark:text-white">{{ $contact['value'] }}</p>
                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">{{ $contact['note'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 px-6 py-4 text-sm text-gray-600 dark:border-white/10 dark:text-gray-300">
            If you need urgent help, please use the primary WhatsApp number first so we can respond quickly.
        </div>
    </div>
</div>
@endsection
