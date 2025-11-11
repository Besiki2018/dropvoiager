@if(!empty($location_category) and !empty($translation->surrounding))
    @if(!empty($line_top))
        <div class="mt-20 mb-40">
            <div class="border-top-light"></div>
        </div>
    @endif
    <div class="gotrip-surrounding">
        <div class="location-title">
            <div class="row x-gap-40 y-gap-40">
                <div class="col-auto">
                    <h3 class="text-22 fw-500">{{__("What's Nearby")}}</h3>
                </div>
            </div>
            <div class="row pt-20">
                @foreach($location_category as $category)
                    @if(!empty($translation->surrounding[$category->id]))
                        <h6 class="font-weight-bold mb-3"><i class="{{clean($category->icon_class)}} "></i> {{$category->location_category_translations->name??$category->name}}</h6>
                        @foreach($translation->surrounding[$category->id] as $item)
                            <div class="row mb-3">
                                <div class="col-lg-4">{{$item['name']}} ({{$item['value']}}{{$item['type']}})</div>
                                <div class="col-lg-8">{{$item['content']}}</div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>

        </div>
    </div>
    <div class="bravo-hr"></div>
@endif