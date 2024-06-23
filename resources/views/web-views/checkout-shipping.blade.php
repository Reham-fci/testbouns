@extends('layouts.front-end.app')

@section('title',translate('Shipping Address Choose'))

@push('css_or_js')
    <style>
        .btn-outline {
            border-color: {{$web_config['primary_color']}} ;
        }

        .btn-outline {
            color: #020512;
            border-color: {{$web_config['primary_color']}}    !important;
        }

        .btn-outline:hover {
            color: white;
            background: {{$web_config['primary_color']}};

        }

        .btn-outline:focus {
            border-color: {{$web_config['primary_color']}}    !important;
        }

        #location_map_canvas {
            height: 100%;
        }

        @media only screen and (max-width: 768px) {
            /* For mobile phones: */
            #location_map_canvas {
                height: 200px;
            }
        }



        .city-non-active{
                display: none;
        }
        .city-active{

            display: block;
        }
    </style>
@endpush

@section('content')
@php($billing_input_by_customer=\App\CPU\Helpers::get_business_settings('billing_input_by_customer'))
    <div class="container pb-5 mb-2 mb-md-4 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <div class="col-md-12 mb-5 pt-5">
                <div class="feature_header">
                    <span>{{ translate('shipping')}} {{$billing_input_by_customer==1?translate('and').' '.translate('billing'):' '}} {{translate('address')}}</span>
                </div>
            </div>
            <section class="col-lg-8">
                <hr>
                <div class="checkout_details mt-3">
                    <!-- Steps-->
                @include('web-views.partials._checkout-steps',['step'=>2])
                <!-- Shipping methods table-->
                    <h2 class="h4 pb-3 mb-2 mt-5">{{ translate('choose_shipping_address')}}</h2>
                    @php($shipping_addresses=\App\Model\ShippingAddress::where('customer_id',auth('customer')->id())->where('is_billing',0)->get())
                    <form method="post" action="" id="address-form">

                        <div class="card-body" style="padding: 0!important;">

                            <div class="d-none alert alert-danger" role="alert">

                            </div>
                            <ul class="list-group">
                                @foreach($shipping_addresses as $key=>$address)
                                    <li class="list-group-item mb-2 mt-2"
                                        style="cursor: pointer;background: rgba(245,245,245,0.51)"
                                        onclick="$('#sh-{{$address['id']}}').prop( 'checked', true )">

                                        <input type="hidden" value="{{$address['zip']}}" name="_area" class="address_area" >
                                        <input type="hidden" value="{{$address['city']}}" name="_city" class="address_city" >
                                        <input type="radio" name="shipping_method_id"
                                               id="sh-{{$address['id']}}"
                                               value="{{$address['id']}}" {{$key==0?'checked':''}}>
                                        <span class="checkmark"
                                              style="margin-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 10px"></span>
                                        <label class="badge"
                                               style="background: {{$web_config['primary_color']}}; color:white !important;">{{$address['address_type']}}</label>
                                        <small>
                                            <i class="fa fa-phone"></i> {{$address['phone']}}
                                        </small>
                                        <hr>
                                        <span>{{ translate('contact_person_name')}}: {{$address['contact_person_name']}}</span><br>
                                        <span>{{ translate('address')}} : {{$address['address']}}, {{$address['city']}}, {{$address['zip']}}.</span>
                                    </li>
                                @endforeach
                                <li class="list-group-item mb-2 mt-2" onclick="anotherAddress()">
                                    <input type="radio" name="shipping_method_id"
                                           id="sh-0" value="0" data-toggle="collapse"
                                           data-target="#collapseThree" {{$shipping_addresses->count()==0?'checked':''}}>
                                    <span class="checkmark"
                                          style="margin-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 10px"></span>

                                    <button type="button" class="btn btn-outline" data-toggle="collapse"
                                            data-target="#collapseThree">{{ translate('Another')}} {{ translate('address')}}
                                    </button>
                                    <div id="accordion">
                                        <div id="collapseThree"
                                             class="collapse {{$shipping_addresses->count()==0?'show':''}}"
                                             aria-labelledby="headingThree"
                                             data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputEmail1">{{ translate('contact_person_name')}}
                                                        <span style="color: red">*</span></label>
                                                    <input type="text" class="form-control"
                                                           name="contact_person_name" {{$shipping_addresses->count()==0?'required':''}}>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">{{ translate('Phone')}}
                                                        <span
                                                            style="color: red">*</span></label>
                                                    <input type="text" class="form-control"
                                                           name="phone" {{$shipping_addresses->count()==0?'required':''}}>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputPassword1">{{ translate('address')}} {{ translate('Type')}}</label>
                                                    <select class="form-control" name="address_type">
                                                        <option
                                                            value="permanent">{{ translate('Permanent')}}</option>
                                                        <option value="home">{{ translate('Home')}}</option>
                                                        <option
                                                            value="others">{{ translate('Others')}}</option>
                                                    </select>
                                                </div>

                                                {{--  --}}

                                                <div class="form-group">
                                                    <label for="zip_code">{{translate('zip_code')}}</label>


                                                    <select class="form-select form-control select-areas address_city" name="city" type="zip" id="si-city"
                                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                        >
                                                        <option ></option>
                                                        @foreach ($governorates as $governorate)
                                                            <option  value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="city">{{translate('City')}}</label>



                                                    <select class="form-select form-control select-areas address_area" name="zip" type="country" id="si-area"
                                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                        >
                                                        <option ></option>
                                                        @foreach ($cities as $city)
                                                            <option class="city-non-active " data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                                        @endforeach
                                                    </select>
                                                    {{--  --}}
                                                </div>
                                                {{--  --}}

                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputEmail1">{{ translate('address')}}<span
                                                            style="color: red">*</span></label>
                                                    <textarea class="form-control" id="address"
                                                              type="text"
                                                              name="address" {{$shipping_addresses->count()==0?'required':''}}></textarea>
                                                </div>
                                                @php($default_location=\App\CPU\Helpers::get_business_settings('default_location'))
                                                <div class="form-group">
                                                    <input id="pac-input" class="controls rounded"
                                                           style="height: 3em;width:fit-content;"
                                                           title="{{translate('search_your_location_here')}}"
                                                           type="text"
                                                           placeholder="{{translate('search_here')}}"/>
                                                    <div style="height: 200px;" id="location_map_canvas"></div>
                                                </div>
                                                 <div class="form-check" style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.25rem;">
                                                    <input type="checkbox" name="save_address" class="form-check-input"
                                                           id="exampleCheck1">
                                                    <label class="form-check-label" for="exampleCheck1" style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.09rem">
                                                        {{ translate('save_this_address')}}
                                                    </label>
                                                </div>
                                                <input type="hidden" id="latitude"
                                                       name="latitude" class="form-control d-inline"
                                                       placeholder="Ex : -94.22213"
                                                       value="{{$default_location?$default_location['lat']:0}}" required
                                                       readonly>
                                                <input type="hidden"
                                                       name="longitude" class="form-control"
                                                       placeholder="Ex : 103.344322" id="longitude"
                                                       value="{{$default_location?$default_location['lng']:0}}" required
                                                       readonly>

                                                <button type="submit" class="btn btn-primary" style="display: none"
                                                        id="address_submit"></button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </form>


                    <div style="display: {{$billing_input_by_customer==1?'':'none'}}">
                        <!-- billing methods table-->
                        <h2 class="h4 pb-3 mb-2 mt-5">{{ translate('choose_billing_address')}}</h2>

                        @php($billing_addresses=\App\Model\ShippingAddress::where('customer_id',auth('customer')->id())->where('is_billing',1)->get())
                        <form method="post" action="" id="billing-address-form">

                            <div class="form-check"
                                style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.25rem;">
                                <input type="checkbox" id="same_as_shipping_address" onclick="hide_billingAddress()"
                                    name="same_as_shipping_address" class="form-check-input" {{$billing_input_by_customer==1?'':'checked'}}>
                                <label class="form-check-label"
                                    style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.09rem">
                                    {{ translate('same_as_shipping_address')}}
                                </label>
                            </div>
                            <div id="hide_billing_address" class="card-body" style="padding: 0!important;">
                                <ul class="list-group">
                                    @foreach($billing_addresses as $key=>$address)

                                        <li class="list-group-item mb-2 mt-2"
                                            style="cursor: pointer;background: rgba(245,245,245,0.51)"
                                            onclick="$('#bh-{{$address['id']}}').prop( 'checked', true )">
                                            <input type="radio" name="billing_method_id"
                                                id="bh-{{$address['id']}}"
                                                value="{{$address['id']}}" {{$key==0?'checked':''}}>
                                            <span class="checkmark"
                                                style="margin-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 10px"></span>
                                            <label class="badge"
                                                style="background: {{$web_config['primary_color']}}; color:white !important;">{{$address['address_type']}}</label>
                                            <small>
                                                <i class="fa fa-phone"></i> {{$address['phone']}}
                                            </small>
                                            <hr>
                                            <span>{{ translate('contact_person_name')}}: {{$address['contact_person_name']}}</span><br>
                                            <span>{{ translate('address')}} : {{$address['address']}}, {{$address['city']}}, {{$address['zip']}}.</span>
                                        </li>
                                    @endforeach
                                    <li class="list-group-item mb-2 mt-2" onclick="billingAddress()">
                                        <input type="radio" name="billing_method_id"
                                            id="bh-0" value="0" data-toggle="collapse"
                                            data-target="#billing_model" {{$billing_addresses->count()==0?'checked':''}}>
                                        <span class="checkmark"
                                            style="margin-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 10px"></span>

                                        <button type="button" class="btn btn-outline" data-toggle="collapse"
                                                data-target="#billing_model">{{ translate('Another')}} {{ translate('address')}}
                                        </button>
                                        <div id="accordion">
                                            <div id="billing_model"
                                                class="collapse {{$billing_addresses->count()==0?'show':''}}"
                                                aria-labelledby="headingThree"
                                                data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label
                                                            for="exampleInputEmail1">{{ translate('contact_person_name')}}
                                                            <span style="color: red">*</span></label>
                                                        <input type="text" class="form-control"
                                                            name="billing_contact_person_name" {{$billing_addresses->count()==0?'required':''}}>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">{{ translate('Phone')}}
                                                            <span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control"
                                                            name="billing_phone" {{$billing_addresses->count()==0?'required':''}}>
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            for="exampleInputPassword1">{{ translate('address')}} {{ translate('Type')}}</label>
                                                        <select class="form-control" name="billing_address_type">
                                                            <option
                                                                value="permanent">{{ translate('Permanent')}}</option>
                                                            <option value="home">{{ translate('Home')}}</option>
                                                            <option
                                                                value="others">{{ translate('Others')}}</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">{{ translate('City')}}<span
                                                                style="color: red">*</span></label>
                                                        <input type="text" class="form-control"
                                                            name="billing_city" {{$billing_addresses->count()==0?'required':''}}>
                                                    </div>

                                                    <div class="form-group">
                                                        <label
                                                            for="exampleInputEmail1">{{ translate('zip_code')}}
                                                            <span
                                                                style="color: red">*</span></label>
                                                        <input type="number" class="form-control"
                                                            name="billing_zip" {{$billing_addresses->count()==0?'required':''}}>
                                                    </div>

                                                    <div class="form-group">
                                                        <label
                                                            for="exampleInputEmail1">{{ translate('address')}}<span
                                                                style="color: red">*</span></label>
                                                        <textarea class="form-control" id="billing_address"
                                                                type="billing_text"
                                                                name="billing_address" {{$billing_addresses->count()==0?'required':''}}></textarea>
                                                    </div>

                                                    <div class="form-group">
                                                        <input id="pac-input-billing" class="controls rounded"
                                                            style="height: 3em;width:fit-content;"
                                                            title="{{translate('search_your_location_here')}}"
                                                            type="text"
                                                            placeholder="{{translate('search_here')}}"/>
                                                        <div style="height: 200px;" id="location_map_canvas_billing"></div>
                                                    </div>
                                                    <div class="form-check" style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.25rem;">
                                                        <input type="checkbox" name="save_address_billing" class="form-check-input"
                                                            id="save_address_billing">
                                                        <label class="form-check-label" for="save_address_billing" style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 1.09rem">
                                                            {{ translate('save_this_address')}}
                                                        </label>
                                                    </div>
                                                    <input type="hidden" id="billing_latitude"
                                                        name="billing_latitude" class="form-control d-inline"
                                                        placeholder="Ex : -94.22213"
                                                        value="{{$default_location?$default_location['lat']:0}}" required
                                                        readonly>
                                                    <input type="hidden"
                                                        name="billing_longitude" class="form-control"
                                                        placeholder="Ex : 103.344322" id="billing_longitude"
                                                        value="{{$default_location?$default_location['lng']:0}}" required
                                                        readonly>

                                                    <button type="submit" class="btn btn-primary" style="display: none"
                                                            id="address_submit"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </form>
                    </div>




                    <!-- Navigation (desktop)-->
                    <div class="row">
                        <div class="col-6">
                            <a class="btn btn-secondary btn-block" href="{{route('shop-cart')}}">
                                <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'right' : 'left'}} mt-sm-0 mx-1"></i>
                                <span class="d-none d-sm-inline">{{ translate('shop_cart')}}</span>
                                <span class="d-inline d-sm-none">{{ translate('shop_cart')}}</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a class="btn btn-primary btn-block" href="javascript:" onclick="checkLimit()">
                                <span class="d-none d-sm-inline">{{ translate('proceed_payment')}}</span>
                                <span class="d-inline d-sm-none">{{ translate('Next')}}</span>
                                <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left' : 'right'}} mt-sm-0 mx-1"></i>
                            </a>
                        </div>
                    </div>
                    <!-- Sidebar-->
                </div>
            </section>
            @include('web-views.partials._order-summary')
        </div>
    </div>
@endsection

@push('script')

    <script>
        function anotherAddress() {
            $('#sh-0').prop('checked', true);
            $("#collapseThree").collapse();
        }

        function billingAddress() {
            $('#bh-0').prop('checked', true);
            $("#billing_model").collapse();
        }

    </script>
    <script>
        function hide_billingAddress() {
            let check_same_as_shippping = $('#same_as_shipping_address').is(":checked");
            console.log(check_same_as_shippping);
            if (check_same_as_shippping) {
                $('#hide_billing_address').hide();
            } else {
                $('#hide_billing_address').show();
            }
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{\App\CPU\Helpers::get_business_settings('map_api_key')}}&libraries=places&v=3.49"></script>
    <script>

        $('#si-city').on('change',function(){
            var cities = $('#si-city option:selected');
            $('#si-area option').removeClass('city-active');
            for (let index = 0; index < cities.length; index++) {
                var city = $(cities[index]).val();
                $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
            }
        })

        $('#si-city2').on('change',function(){
            var city = $('#si-city2 option:selected').val();
            $('#si-area2 option').removeClass('city-active');
            $('#si-area2 option[ data-parent="'+city+'"]').addClass('city-active');
        })
        function initAutocomplete() {

            let markers = [];
            var myLatLng = { lat: {{$shipping_latitude??'-33.8688'}}, lng: {{$shipping_longitude??'151.2195'}} };

            const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                center: { lat: {{$shipping_latitude??'-33.8688'}}, lng: {{$shipping_longitude??'151.2195'}} },
                zoom: 13,
                mapTypeId: "roadmap",
            });

            var marker = new google.maps.Marker({
                map,
                position: myLatLng,
                map: map,
            });

            markers.forEach((marker) => {
                marker.setMap(null);
            });
            markers = [];

            // marker.setMap( map );
            markers.push(marker);
            var geocoder = geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function (mapsMouseEvent)
            {
                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                var coordinates = JSON.parse(coordinates);
                var latlng = new google.maps.LatLng( coordinates['lat'], coordinates['lng'] ) ;
                // marker.setPosition( latlng );
                // map.panTo( latlng );
                var myLatLng = { lat:  coordinates['lat'], lng: coordinates['lng'] };

                var marker = new google.maps.Marker({
                    map,
                    position: myLatLng,
                    map: map,
                });

                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];

                // marker.setMap( map );
                markers.push(marker);

                document.getElementById('latitude').value = coordinates['lat'];
                document.getElementById('longitude').value = coordinates['lng'];

                console.log("test here change 1");
                geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                    console.log("test here change 2");
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {

                            console.log(results);
                            document.getElementById('address').value = results[1].formatted_address;
                            console.log(results[1].formatted_address);
                        }
                    }
                });
            });

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) {
                return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) =>
                {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    var mrkr = new google.maps.Marker({
                        map,
                        title: place.name,
                        position: place.geometry.location,
                    });
                    // var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);

                    document.getElementById('latitude').value  = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();
                    document.getElementById('address').value = document.getElementById('pac-input').value;
                    google.maps.event.addListener(mrkr, "click", function (event) {
                        console.log('click place');
                        document.getElementById('address').value = document.getElementById('pac-input').value;
                        document.getElementById('latitude').value  = this.position.lat();
                        document.getElementById('longitude').value = this.position.lng();

                    });

                    markers.push(mrkr);

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        };
        $(document).on('ready', function () {
            initAutocomplete();

        });

        $(document).on("keydown", "input", function(e) {
        if (e.which==13) e.preventDefault();
        });
    </script>

    <script>
        function checkLimit(){
            var address_area = $('[name="shipping_method_id"]:checked').parent('li').find('.address_area').val()
            var address_city = $('[name="shipping_method_id"]:checked').parent('li').find('.address_city').val()
            $.ajax({
                type: "POST",
                url: '{{route('checkout-Limit')}}',
                data: {
                    _token: "{{ csrf_token() }}",
                    area:address_area,
                    city:address_city
                },
                dataType: "json",
                success: (response) => {
                    if(response.status == 1){
                        $('.alert-danger').addClass('d-none');
                        proceed_to_next();
                    }
                    else{
                        $('.alert-danger').text(`{{translate("OrderLimitPrice")}}`+response.limit);
                        $('.alert-danger').removeClass('d-none');
                    }
                }
            });
        }

        function proceed_to_next()
        {
            let billing_addresss_same_shipping = $('#same_as_shipping_address').is(":checked");

            let allAreFilled = true;
            document.getElementById("address-form").querySelectorAll("[required]").forEach(function (i) {
                if (!allAreFilled) return;
                if (!i.value) allAreFilled = false;
                if (i.type === "radio") {
                    let radioValueCheck = false;
                    document.getElementById("address-form").querySelectorAll(`[name=${i.name}]`).forEach(function (r) {
                        if (r.checked) radioValueCheck = true;
                    });
                    allAreFilled = radioValueCheck;
                }
            });

            //billing address saved
            let allAreFilled_shipping = true;

            if (billing_addresss_same_shipping != true) {

                document.getElementById("billing-address-form").querySelectorAll("[required]").forEach(function (i) {
                    if (!allAreFilled_shipping) return;
                    if (!i.value) allAreFilled_shipping = false;
                    if (i.type === "radio") {
                        let radioValueCheck = false;
                        document.getElementById("billing-address-form").querySelectorAll(`[name=${i.name}]`).forEach(function (r) {
                            if (r.checked) radioValueCheck = true;
                        });
                        allAreFilled_shipping = radioValueCheck;
                    }
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('customer.choose-shipping-address')}}',
                // dataType: 'json',
                data: {
                    shipping: $('#address-form').serialize(),
                    billing: $('#billing-address-form').serialize(),
                    billing_addresss_same_shipping: billing_addresss_same_shipping
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        location.href = '{{route('checkout-payment')}}';
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
                error: function () {
                    toastr.error('{{translate('Please fill all required fields of shipping/billing address')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });


        }
    </script>
@endpush
