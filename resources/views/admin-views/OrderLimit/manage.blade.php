@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Coupon Edit'))
@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    
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
    <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    @if($id)
                        <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CPU\translate('orderLimit')}} {{\App\CPU\translate('update')}}</h1>
                    @else
                        <h1 class="page-header-title"><i class="tio-edit"></i> 
                            {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('orderLimit')}}    
                        </h1>
                    @endif

                </div>
            </div>
        </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.OrderLimit.insertandUpdate',[$id])}}" method="post">
                        @csrf
                        <div class="row">
                            
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('limit')}}</label>
                                    <input name="id" value="{{$id}}" type="hidden">
                                    <input type="number" step="any" name="limit" 
                                        value="<?php if(isset($orderLimit->Limit)){echo $orderLimit->Limit;} ?>"
                                        class="form-control" id="code"
                                        placeholder="" required>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            @foreach($governorates as $governorate)
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-check-input city" type="checkbox" value="{{$governorate->id}}">
                                        <label class="form-check-label" for="flexCheckDefault" style="margin: 0 20px;">
                                            {{$governorate->governorate_name_ar}}
                                        </label>                                    
                                    </div>
                                </div>
                                <div class="col-12 row">
                                    @foreach($governorate->areas as $area)
                                        <div class="col-2">
                                            <div class="form-group">
                                                <input class="form-check-input area" name="area[]" type="checkbox" value="{{$area->id}}" data-value="{{$governorate->id}}"
                                                <?php if(in_array($area->id , $areas)){echo "checked";}?>
                                                >
                                                <label class="form-check-label " for="flexCheckDefault" style="margin: 0 20px;">
                                                    {{$area->city_name_ar}}
                                                </label>                                    
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
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
        $('.city').on('click',function(e){
            var id = $(this).val();
            var checked = $(this).prop('checked');
            console.log(checked);
            if(checked){
            $('input[data-value="'+id+'"]').prop('checked' , true);
            }
            else{
            $('input[data-value="'+id+'"]').prop('checked' , false);
            }
        });
    </script>
        
        
@endpush
