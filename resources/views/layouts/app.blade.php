@include("Layout::app")

  @if(env('DISABLE_INDEXING') === 'true')
        <meta name="robots" content="noindex, nofollow">
    @endif