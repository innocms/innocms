@guest
    <div class="m-4 text-center text-muted">
        <p>Powered By <a href="https://www.innocms.com" class="text-muted">InnoCMS</a></p>
    </div>
@else

    <div class="text-center user-select-none my-4 d-none d-lg-block">
        <p class="small mb-0">
            Powered By
            <a href="https://www.innocms.com" target="_blank" rel="noopener">InnoCMS</a>
            {{\Orchid\Platform\Dashboard::VERSION}}
            &copy; {{date('Y')}} All Rights Reserved
        </p>
    </div>
@endguest
