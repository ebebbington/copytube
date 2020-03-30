<form
    @if (isset($id))
    id="{{ $id }}"
    @endif
    @if (isset($encType))
    enctype="{{ $encType }}"
    @endif
>
    {{ csrf_field() }}
    <fieldset>
        <legend class="form-title">{{ $formTitle }}</legend>
        {{ $slot }}
    </fieldset>
</form>
