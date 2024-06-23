@extends('layouts.front-end.app')

@section('title',translate('My Loyalty Point'))

@push('css_or_js')
    <style>
        .widget-categories .accordion-heading > a:hover {
            color: #FFD5A4 !important;
        }

        .widget-categories .accordion-heading > a {
            color: #FFD5A4;
        }

        body {
            font-family: 'Titillium Web', sans-serif;
        }

        .card {
            border: none
        }

        .totals tr td {
            font-size: 13px
        }

        .product-qty span {
            font-size: 14px;
            color: #6A6A6A;
        }

        .spandHeadO {
            color: #FFFFFF !important;
            font-weight: 600 !important;
            font-size: 14px;

        }

        .tdBorder {
            border- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 1px solid #f7f0f0;
            text-align: center;
        }

        .bodytr {
            text-align: center;
            vertical-align: middle !important;
        }

        .sidebar h3:hover + .divider-role {
            border-bottom: 3px solid {{$web_config['primary_color']}}                                   !important;
            transition: .2s ease-in-out;
        }

        tr td {
            padding: 10px 8px !important;
        }

        td button {
            padding: 3px 13px !important;
        }

        @media (max-width: 600px) {
            .sidebar_heading {
                background: {{$web_config['primary_color']}};
            }

            .orderDate {
                display: none;
            }

            .sidebar_heading h1 {
                text-align: center;
                color: aliceblue;
                padding-bottom: 17px;
                font-size: 19px;
            }
        }
    </style>
@endpush

@section('content')

    <div class="container rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9 mt-2 sidebar_heading">
                <h1 class="h3  mb-0 p-3 float-{{Session::get('direction') === "rtl" ? 'right' : 'left'}} headerTitle">{{translate('my_loyalty_point')}}</h1>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->
            <section class="col-lg-9 col-md-9">
                @php
                    $wallet_status = App\CPU\Helpers::get_business_settings('wallet_status');
                    $loyalty_point_status = App\CPU\Helpers::get_business_settings('loyalty_point_status');
                @endphp
                <div class="card box-shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span>
                                    {{translate('loyalty_point_history')}}
                                </span>
                            </div>
                            <div>
                                <span>
                                    {{translate('total_loyalty_point')}} : {{$total_loyalty_point}}
                                </span>
                            </div>
                            <div>
                                @if ($wallet_status == 1 && $loyalty_point_status == 1)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#convertToCurrency">
                                    {{translate('convert_to_currency')}}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow: auto">
                            <table class="table">
                                <thead>
                                <tr style="background-color: #6b6b6b;">
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO ">{{translate('sl#')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO">{{translate('transaction_type')}} </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO">{{translate('credit')}} </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{translate('debit')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{translate('balance')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{translate('date')}}</span></div>
                                    </td>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($loyalty_point_list as $key=>$item)
                                    <tr>
                                        <td class="bodytr">
                                            {{$loyalty_point_list->firstItem()+$key}}
                                        </td>
                                        <td class="bodytr orderDate"><span class="text-capitalize">{{str_replace('_', ' ',$item['transaction_type'])}}</span></td>
                                        <td class="bodytr orderDate"><span class="">{{ $item['credit']}}</span></td>
                                        <td class="bodytr orderDate"><span class="">{{ $item['debit']}}</span></td>
                                        <td class="bodytr orderDate"><span class="">{{ $item['balance']}}</span></td>
                                        <td class="bodytr orderDate"><span class="">{{$item['created_at']}}</span></td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if($loyalty_point_list->count()==0)
                                <center class="mt-3 mb-2">{{translate('no_transaction_found')}}</center>
                            @endif
                            <div class="card-footer">
                                {{$loyalty_point_list->links()}}
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>


  <!-- Modal -->
  <div class="modal fade" id="convertToCurrency" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{translate('convert_to_currency')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('loyalty-exchange-currency')}}" method="POST">
            @csrf
        <div class="modal-body">
            <div>
                <span>
                    {{translate('your loyalty point will convert to currency and transfer to your wallet')}}
                </span>
            </div>
            <div class="text-center">
                <span class="text-warning">
                    {{translate('minimum point for convert to currency is :')}} {{App\CPU\Helpers::get_business_settings('loyalty_point_minimum_point')}} {{translate('point')}}
                </span>
            </div>
            <div class="text-center">
                <span >
                    {{App\CPU\Helpers::get_business_settings('loyalty_point_exchange_rate')}} {{translate('point')}} = {{\App\CPU\Helpers::currency_converter(1)}}
                </span>
            </div>

            <div class="form-row">
                <div class="form-group col-12">

                    <input class="form-control" type="number" id="city" name="point" required>
                </div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('close')}}</button>
          <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
        </div>
    </form>
      </div>
    </div>
  </div>
@endsection

@push('script')

@endpush
