@props(['messages'])

@if ($messages)
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
        <p>{{ session('message') }}</p>
    </div>
@endif
