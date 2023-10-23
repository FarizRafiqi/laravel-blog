@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Postingan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('posts.store') }}" method="POST" class="bg-white p-4 rounded-md"
                  x-data="{ title: '', slug: '' }" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label for="title"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                    <input type="text"
                           id="title"
                           x-model="title"
                           @input.debounce="generateSlug"
                           name="title"
                           value="{{ old('title') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           placeholder="Masukkan judul">
                    @error('title')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                    <input type="text"
                           x-model="slug"
                           name="slug"
                           id="slug"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:cursor-not-allowed"
                           disabled
                    >
                </div>

                <div class="mb-6">
                    <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Konten
                    </label>
                    <input id="content" type="hidden" name="content">
                    <trix-editor input="content"
                                 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 break-words"
                                 placeholder="Tulis konten...">{{ old('content') }}</trix-editor>

                    @error('content')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="category"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <select id="category"
                            name="category_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected disabled>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id === old('category_id') ? 'selected' : '' }}
                            >{{ $category->name }}</option>
                        @endforeach
                    </select>

                    @error('category_id')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="image">
                        Gambar
                    </label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        id="image" type="file" name="image">

                    @error('image')
                    <span class="text-red">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <input id="checked-checkbox" type="checkbox" name="is_published" value="true"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="checked-checkbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Terbitkan</label>
                    </div>
                </div>

                <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Submit
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
<script>
    const generateSlug = () => {
        // Make a request to the server to get the slug
        fetch('/posts/generate-slug', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({title: this.title.value}),
        })
            .then(response => response.json())
            .then(data => {
                if (data.slug) {
                    this.slug.value = data.slug;
                } else {
                    this.slug.value = "";
                }
            });
    }

    document.addEventListener('trix-file-accept', function (e) {
        e.preventDefault()
    })
</script>
