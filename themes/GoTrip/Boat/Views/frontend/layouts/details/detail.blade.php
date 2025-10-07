@if($row->getGallery())
    @include('Layout::common.detail.gallery5',['galleries' => $row->getGallery()])
@endif
<div class="row x-gap-80 y-gap-40 pt-40">
    <div class="col-12 gotrip-overview">
        <h3 class="text-22 fw-500">{{ __("Overview") }}</h3>
        <div class="text-dark-1 text-15 mt-20 content-text">
            {!! clean($translation->content) !!}
        </div>
        <span class="d-none btn-showmore pointer text-14 text-blue-1 fw-500 underline mt-10">
                {{ __("Show More") }}
            </span>
    </div>
    @include('Boat::frontend.layouts.details.specs')
    @include('Boat::frontend.layouts.details.attributes')
</div>
