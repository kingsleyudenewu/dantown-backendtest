@props(['messages'])

@if ($messages)
    <div class="bg-green-200 border-l-4 border-green-500 p-4" role="alert">
        <p>{{ session('messages') }}</p>
    </div>
@endif
