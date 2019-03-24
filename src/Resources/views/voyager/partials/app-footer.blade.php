<footer class="app-footer">
    <div class="site-footer-right">
        Admin Module
        @php $version = Voyager::getVersion(); @endphp
        @if (!empty($version))
            - {{ $version }}
        @endif
    </div>
</footer>
