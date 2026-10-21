<footer class="footer non-printable">
    {{ $app_setting->footer ?: '© '.date('Y').' '.config('app.name').'. '.trans('lang.all_rights_reserved').'.' }}
</footer>
