@if ($errors->any())
<script>
    (function () {
        const errorFields = @json(array_keys($errors->toArray()));
        const passwordFields = ['current_password', 'new_password', 'new_password_confirm'];

        if (errorFields.some(f => passwordFields.includes(f))) {
            openModal('passModal');
        }
    })();
</script>
@endif
