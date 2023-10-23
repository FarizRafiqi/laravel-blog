<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">{{ \Str::title($post->title) }}</h1>

            <p class="text-gray-400 mt-10">Author: {{ $post->user->name }}</p>

            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="mt-4">

            <div class="mt-4">
                {!! $post->content !!}
            </div>
        </div>
    </div>
</x-app-layout>
