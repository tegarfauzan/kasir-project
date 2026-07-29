@props(['value'])

<label {{ $attributes->merge(['class' => 'app-label block']) }}>
    {{ $value ?? $slot }}
</label>
