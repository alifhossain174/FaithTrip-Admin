@extends('master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td:nth-child(1) {
            font-weight: 600;
        }

        table.dataTable tbody {
            text-align: center !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        table#DataTables_Table_0 img {
            transition: all .2s linear;
        }

        img.gridProductImage:hover {
            scale: 2;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0" style="font-size: 18px">View Bank Accounts</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <label id="customFilter">
                                <a href="{{url('add/bank/account')}}" style="margin-left: 5px" class="btn btn-success btn-sm"><b><i class="fas fa-plus"></i> Add Bank Account</b></a>
                            </label>

                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-bordered mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Bank Name</th>
                                            <th class="text-center">Bank Account</th>
                                            <th class="text-center">Branch</th>
                                            <th class="text-center">Routing No</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            {{-- </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('footer_js')
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('view/bank/accounts') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'bank_name',
                    name: 'bank_name'
                },
                {
                    data: 'acc_no',
                    name: 'acc_no'
                },
                {
                    data: 'branch_name',
                    name: 'branch_name'
                },
                {
                    data: 'routing_no',
                    name: 'routing_no'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });
        $(".dataTables_filter").append($("#customFilter"));
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to delete !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/bank/account') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Bank Account has been Deleted");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection
