@extends('layouts.back-end.app')


@section('title', \App\CPU\translate('Add')." ".\App\CPU\translate('New')." ".\App\CPU\translate('client'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
@endpush
@push('css_or_js')
<style>
    .forAll{
        display: none;
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
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-add-circle-outlined"></i> 
                            {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('client')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Content Row -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.customer.submit')}}" method="post">
                            @csrf
                            <input type="hidden"  name="id" value="{{$id}}">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-fn">{{\App\CPU\translate('first_name')}}</label>
                                        <input class="form-control"  type="text" value="{{($client ? $client['f_name']: "")}}" name="f_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your first name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-ln">{{\App\CPU\translate('last_name')}}</label>
                                        <input class="form-control" type="text"  value="{{($client ? $client['l_name']: "")}}" name="l_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your last name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-email">{{\App\CPU\translate('email_address')}}</label>
                                        <input class="form-control" type="email"  value="{{($client ? $client['email']: "")}}" name="email"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter valid email address')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-phone">{{\App\CPU\translate('phone_number')}}
                                            <small class="text-primary">( * {{\App\CPU\translate('country_code_is_must')}} {{\App\CPU\translate('like_for_BD_880')}} )</small></label>
                                        <input class="form-control" type="text"   value="{{($client ? $client['phone']: "")}}" name="phone"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your phone number')}}!</div>
                                    </div>
                                </div>

                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control"  name="password" type="password" id="si-password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   <?php if(($id==0)){echo "required";}?>
                                                   >
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show')}} {{\App\CPU\translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('confirm_password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control"  name="con_password" type="password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   id="si-password"
                                                   <?php if(($id==0)){echo "required";}?>
                                                   >
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"
                                                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show')}} {{\App\CPU\translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                </div>

                                {{-- here --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('city')}}</label>
                                        <div class="password-toggle">
                                            
                                            
                                            <select class="form-select form-control"  name="city" type="city" id="si-city"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($governorates as $governorate)
                                                    <option  {{($client ? ($governorate->id == $client['city'] ? "selected" : "") : "")}} value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('area')}}</label>
                                        <div class="password-toggle">
                                            
                                            
                                            <select class="form-select form-control" name="area" type="country" id="si-area"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($cities as $city)
                                                    <option class="city-non-active" {{($client ? ($city->id == $client['area'] ? "selected" : "") : "")}} data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('Type')}}</label>
                                        <div class="password-toggle">
                                            
                                            
                                            <select class="form-select form-control"  name="type" type="type" id="si-Type"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($customertypes as $customertype)
                                                    <option {{($client ? ($customertype->id == $client['type'] ? "selected" : "") : "")}} value="{{$customertype->id}}">{{$customertype->ar_name}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('getFrom')}}</label>
                                        <div class="password-toggle">
                                            
                                            
                                            <select class="form-select form-control"  name="getFrom" type="getFrom" id="si-Type"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                >
                                                <option ></option>
                                                <option {{($client ? ("type_facebook" == $client['getFrom'] ? "selected" : "") : "")}} value="type_facebook">
                                                    {{\App\CPU\translate('type_facebook')}}
                                                </option>
                                                <option {{($client ? ("type_representative" == $client['getFrom'] ? "selected" : "") : "")}} value="type_representative">
                                                    {{\App\CPU\translate('type_representative')}}
                                                </option>
                                                <option {{($client ? ("type_nomination" == $client['getFrom'] ? "selected" : "") : "")}} value="type_nomination">
                                                    {{\App\CPU\translate('type_nomination')}}
                                                </option>
                                                <option {{($client ? ("type_call" == $client['getFrom'] ? "selected" : "") : "")}} value="type_call">
                                                    {{\App\CPU\translate('type_call')}}
                                                </option>
                                                <option {{($client ? ("type_linkdin" == $client['getFrom'] ? "selected" : "") : "")}} value="type_linkdin">
                                                    {{\App\CPU\translate('type_linkdin')}}
                                                </option>
                                                <option {{($client ? ("type_Google" == $client['getFrom'] ? "selected" : "") : "")}} value="type_Google">
                                                    {{\App\CPU\translate('type_Google')}}
                                                </option>
                                                <option {{($client ? ("type_other" == $client['getFrom'] ? "selected" : "") : "")}} value="type_other">
                                                    {{\App\CPU\translate('type_other')}}
                                                </option>
                                            </select>
                                            
                                        </div>
                                    </div>

                                </div>
                                {{-- here --}}
                                <div class="col-md-12 row">
                                    <h3>{{\App\CPU\translate('Alladdress')}}</h3>
                                    <div class="address-list col-sm-12">
                                        @forEach($addresses as $index => $address)
                                            <div class="col-sm-12 address">
                                                <div class="row">
                                                    
            
            
                                                        <div class="form-row col-md-12">
                                                            
                                                            <div class="form-group col-md-6">
                                                                <label for="name">{{\App\CPU\translate('addressAs')}}</label>
                                                                
                                            
                                                                <select class="form-select form-control"  name="address[<?=$index?>][addressAs]" type="addressAs" id="si-city"
                                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                                required>
                                                                    <option selected></option>
                                                                    <option  {{($address->address_type == \App\CPU\translate('vale_addressAspermanent') ? "selected" : "") }} 
                                                                        value="{{\App\CPU\translate('vale_addressAspermanent')}}">
                                                                        {{\App\CPU\translate('addressAspermanent')}}
                                                                    </option>
                                                                    <option  {{($address->address_type == \App\CPU\translate('vale_addressAsHome') ? "selected" : "") }} 
                                                                        value="{{\App\CPU\translate('vale_addressAsHome')}}">
                                                                        {{\App\CPU\translate('addressAsHome')}}
                                                                    </option>
                                                                    <option  {{($address->address_type == \App\CPU\translate('vale_addressAsOffice') ? "selected" : "") }} 
                                                                        value="{{\App\CPU\translate('vale_addressAsOffice')}}">
                                                                        {{\App\CPU\translate('addressAsOffice')}}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label for="name">{{\App\CPU\translate('is_billing')}}</label>
                                                                <select class="form-select form-control"  name="address[<?=$index?>][is_billing]" type="is_billing" id="si-city"
                                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                                required>
                                                                    <option selected></option>
                                                                    <option  {{($address->is_billing == 0 ? "selected" : "")}} 
                                                                        value="0">
                                                                        {{\App\CPU\translate('shipping')}}
                                                                    </option>
                                                                    <option  {{($address->is_billing == 1 ? "selected" : "")}} 
                                                                        value="1">
                                                                        {{\App\CPU\translate('billing')}}
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="name">{{\App\CPU\translate('contact_person_name')}}</label>
                                                                <input type="hidden" value="{{$address->id}}" name="address[<?=$index?>][id]">
                                                                <input class="form-control" type="text" id="" value="{{$address->contact_person_name}}" name="address[<?=$index?>][name]" required>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label for="firstName">{{\App\CPU\translate('Phone')}}</label>
                                                                <input class="form-control" type="text" id="" value="{{$address->phone}}" name="address[<?=$index?>][phone]" required>
                                                            </div>
                        
                                                        </div>
                                                        <div class="form-row col-md-12">
                                                            <div class="form-group col-md-6">
                                                                <label for="address-city">{{\App\CPU\translate('City')}}</label>                                                                
                                            
                                                                <select class="form-select form-control si-city_address"  name="address[<?=$index?>][city]" type="city" data-index="{{$index}}"
                                                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                                    required>
                                                                    <option selected></option>
                                                                    @foreach ($governorates as $governorate)
                                                                        <option  {{($client ? ($governorate->id == $address->city ? "selected" : "") : "")}} value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label for="zip">{{\App\CPU\translate('zip_code')}}</label>
                                                                
                                                                <select class="form-select form-control si-area_address" name="address[<?=$index?>][zip]" type="country" data-index="{{$index}}"
                                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                                required>
                                                                <option selected></option>
                                                                @foreach ($cities as $city)
                                                                    <option class="city-non-active {{($client ? ($city->governorate_id == $address->city ? "city-active" : "") : "")}}" {{($client ? ($city->id == $address->zip ? "selected" : "") : "")}} data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                                                @endforeach
                                                            </select>

                                                            </div>
                                                        </div>

                                                       
                                                        
                                                        
                                                        <div class="form-row col-md-12">
                                                            <div class="form-group col-md-12">
                                                                <label for="address">{{\App\CPU\translate('address')}}</label>
                                                                
                                                                <textarea class="form-control" id="address_{{$index}}"
                                                                                    type="text"  name="address[<?=$index?>][address]" value="{{$address->address}}"  required>{{$address->address}}</textarea>
                                                            </div>
                                                            @php($default_location=\App\CPU\Helpers::get_business_settings('default_location'))
                                                            <div class="form-group col-md-12">
                                                                <input id="pac-input_{{$index}}" class="controls rounded" style="height: 3em;width:fit-content;" title="{{\App\CPU\translate('search_your_location_here')}}" type="text" placeholder="{{\App\CPU\translate('search_here')}}"/>
                                                                <div style="height: 200px;" id="location_map_canvas_<?=$index?>"></div>
                                                            </div>
                                                        </div>
                                                                            
                                                        <input type="hidden" id="latitude_{{$index}}"
                                                        name="address[<?=$index?>][latitude]" class="form-control d-inline"
                                                        placeholder="Ex : -94.22213" value="{{$default_location?$default_location['lat']:0}}" required readonly>
                                                        <input type="hidden"
                                                        name="address[<?=$index?>][longitude]" class="form-control"
                                                        placeholder="Ex : 103.344322" id="longitude_{{$index}}" value="{{$default_location?$default_location['lng']:0}}" required readonly>
                                                
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>                                    
                                        <button class="btn btn-primary m-5" id="add-btn-address">{{\App\CPU\translate('add_new_address')}}</button>
                                    </div>
                                </div>

                            </div>

                            

                            



                            <div class="">
                                <button type="submit" class="btn btn-primary float-right">{{\App\CPU\translate('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        

        
    </div>
@endsection

@push('script')

<script>
    
    var address_index = <?=count($addresses)?> - 1 ;
    
    $('#si-city').on('change',function(){
        var city = $('#si-city option:selected').val();
        $('#si-area option').removeClass('city-active');
        $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
    })
    
    

    $('body').on('change','.si-city_address',function(){
        console.log('change ---- ');
        var city_index = $(this).data('index');
        console.log("city_index ===> " + city_index);
        var city = $('.si-city_address[data-index="'+city_index+'"] option:selected').val();
        console.log("city ===> " + city);
        $('.si-area_address[data-index="'+city_index+'"] option').removeClass('city-active');
        $('.si-area_address[data-index="'+city_index+'"] option[ data-parent="'+city+'"]').addClass('city-active');
    })

    $('#ForAll').on('change',function(){
        if($(this).val() == 1){
            $('.forAll').hide();
        }
        else{
            $('.forAll').show();
        }
    })

    $('body').on('click' , '.Remove-btn-address' , function(e){
        e.preventDefault();
        console.log('click');
        console.log($(this));
        $(this).parents('.address').remove();
    })

    $('#add-btn-address').click(function(e){
        e.preventDefault();
        address_index += 1;
        $('.address-list').append(
            `
            
                <div class="col-sm-12 address">
                    <div class="row map-`+address_index+`">
                        


                            <div class="form-row col-md-12">
                                
                                <div class="form-group col-md-6">
                                    <label for="name">{{\App\CPU\translate('addressAs')}}</label>
                                    
                
                                    <select class="form-select form-control"  name="address[`+address_index+`][addressAs]" type="addressAs" id="si-city"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    required>
                                        <option selected></option>
                                        <option 
                                            value="{{\App\CPU\translate('vale_addressAspermanent')}}">
                                            {{\App\CPU\translate('addressAspermanent')}}
                                        </option>
                                        <option 
                                            value="{{\App\CPU\translate('vale_addressAsHome')}}">
                                            {{\App\CPU\translate('addressAsHome')}}
                                        </option>
                                        <option  
                                            value="{{\App\CPU\translate('vale_addressAsOffice')}}">
                                            {{\App\CPU\translate('addressAsOffice')}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">{{\App\CPU\translate('is_billing')}}</label>
                                    <select class="form-select form-control"  name="address[`+address_index+`][is_billing]" type="is_billing" id="si-city"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    required>
                                        <option selected></option>
                                        <option 
                                            value="0">
                                            {{\App\CPU\translate('shipping')}}
                                        </option>
                                        <option  
                                            value="1">
                                            {{\App\CPU\translate('billing')}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">{{\App\CPU\translate('contact_person_name')}}</label>
                                    <input class="form-control" type="text" id="" name="address[`+address_index+`][name]" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="firstName">{{\App\CPU\translate('Phone')}}</label>
                                    <input class="form-control" type="text" id="" name="address[`+address_index+`][phone]" required>
                                </div>

                            </div>
                            
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="address-city">{{\App\CPU\translate('City')}}</label>                                                                
                
                                    <select class="form-select form-control si-city_address"  name="address[`+address_index+`][city]" type="city" data-index="`+address_index+`"
                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                        required>
                                        <option selected></option>
                                        @foreach ($governorates as $governorate)
                                            <option   value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="zip">{{\App\CPU\translate('zip_code')}}</label>
                                    
                                    <select class="form-select form-control si-area_address" name="address[`+address_index+`][zip]" type="country" data-index="`+address_index+`"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    required>
                                    <option selected></option>
                                    @foreach ($cities as $city)
                                        <option class="city-non-active"  data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                    @endforeach
                                </select>

                                </div>
                            </div>
                            
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="address">{{\App\CPU\translate('address')}}</label>
                                    
                                    <textarea class="form-control" id="address_`+address_index+`"
                                                        type="text"  name="address[`+address_index+`][address]" required></textarea>
                                </div>
                                @php($default_location=\App\CPU\Helpers::get_business_settings('default_location'))
                                <div class="form-group col-md-12">
                                    <input id="pac-input_`+address_index+`" class="controls rounded" style="height: 3em;width:fit-content;" title="{{\App\CPU\translate('search_your_location_here')}}" type="text" placeholder="{{\App\CPU\translate('search_here')}}"/>
                                    <div style="height: 200px;" id="location_map_canvas_`+address_index+`"></div>
                                </div>
                            </div>
                                                
                            <input type="hidden" id="latitude_`+address_index+`"
                            name="address[`+address_index+`][latitude]" class="form-control d-inline"
                            placeholder="Ex : -94.22213" value="{{$default_location?$default_location['lat']:0}}" required readonly>
                            <input type="hidden"
                            name="address[`+address_index+`][longitude]" class="form-control"
                            placeholder="Ex : 103.344322" id="longitude_`+address_index+`" value="{{$default_location?$default_location['lng']:0}}" required readonly>
                    
                    </div>
                    <div class="row">
                        <button class="btn btn-danger  m-5 Remove-btn-address">{{\App\CPU\translate('Remove')}}</button>            
                    </div>
                </div>
            `
        )

        initAutocomplete(address_index , `location_map_canvas_`+address_index);
    })
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{\App\CPU\Helpers::get_business_settings('map_api_key')}}&libraries=places&v=3.49"></script>
    <script>
        
        
        function initAutocomplete(index , mapId , lat = -33.8688 , long = 151.2195 ) {
            
            let markers = [];
            var myLatLng = { lat: lat, lng: long };

            const map = new google.maps.Map(document.getElementById(mapId), {
                center: { lat: lat, lng: long },
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

                document.getElementById('latitude_'+index).value = coordinates['lat'];
                document.getElementById('longitude_'+index).value = coordinates['lng'];

                console.log("test here change 1");
                geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                    console.log("test here change 2");
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            
                            console.log(results);
                            document.getElementById('address_'+index).value = results[1].formatted_address;
                            console.log(results[1].formatted_address);
                        }
                    }
                });
            });

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input_"+index);
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

                    document.getElementById('latitude_'+index).value  = place.geometry.location.lat();
                    document.getElementById('longitude_'+index).value = place.geometry.location.lng();
                    document.getElementById('address_'+index).value = document.getElementById('pac-input_'+index).value;
                    google.maps.event.addListener(mrkr, "click", function (event) {
                        console.log('click place');
                        document.getElementById('address_'+index).value = document.getElementById('pac-input_'+index).value;
                        document.getElementById('latitude_'+index).value  = this.position.lat();
                        document.getElementById('longitude_'+index).value = this.position.lng();

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
            
            @forEach($addresses as $index => $address)
             initAutocomplete({{$index}} , `location_map_canvas_`+{{$index}} , {{$address->latitude}}, {{$address->longitude}});
            @endforeach

        });

        $(document).on("keydown", "input", function(e) {
        if (e.which==13) e.preventDefault();
        });
    </script>

@endpush
