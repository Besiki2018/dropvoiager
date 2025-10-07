@if($translation->general_info)
    <div id="go_to_map_content" class="pt-30 mt-30 border-top-light"></div>
    <div class="row y-gap-20">
        <div class="col-12">
            <h2 class="text-22 fw-500">{{ __('General info') }}</h2>
        </div>
        @if(!empty($translation->general_info))
            @php if(!is_array($translation->general_info)) $translation->general_info = json_decode($translation->general_info,true); @endphp
            @foreach($translation->general_info as $key=>$item)
                <div class="col-xl-3 col-6">
                    <div class="text-15">{{$item['title']}}</div>
                    <div class="fw-500">{{$item['desc']}}</div>
                    <div class="text-15 text-light-1">{{$item['content']}}</div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="mt-30 border-top-light"></div>
@endif
