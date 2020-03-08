<form>
    {{ csrf_field() }}
    <fieldset>
        <legend class="form-title">{{ $formTitle }}</legend>
        {{ $slot }}
    </fieldset>
</form>