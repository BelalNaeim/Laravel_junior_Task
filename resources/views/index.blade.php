@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
@endsection
@section('content')
    @if (\Session::has('message'))
        <div class="alert alert-success mt-2">
            <ul>
                <li>{!! \Session::get('message') !!}</li>
            </ul>
        </div>
    @endif
    <h2 class="text-center">Transaction Details</h2>
    <br />
    <div align="right">
        <button type="button" name="add" id="add_data" class="btn btn-success btn-sm">Add</button>
    </div>
    <br />
    <div class="container mr-5 ml-5 mt-5">
        <table id="transactionTable" border="1px solid #000">
            <thead>
                <th>Id</th>
                <th>Payment_id</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
                <th>created_Date</th>
            </thead>
            <tbody>
                @foreach ($Transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->payment_id }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->currency }}</td>
                        <td>{{ $transaction->status }}</td>
                        <td>{{ $transaction->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="transactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" id="transaction_form">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal">&times;</button>
                        <h4>Add Transaction</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <span id="form_output"></span>
                        <div class="form-group"><label for="">Enter Payment Id</label><input type="text"
                                name="payment_id" id="payment_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Enter Amount</label>
                            <input type="text" name="amount" id="amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Enter currency</label>
                            <input type="text" name="currency" id="currency" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Enter status</label>
                            <select class="form-control m-bot15" name="status">
                                <option value="COMPLETED" @selected(T = T)>
                                    COMPLETED
                                </option>
                                <option value="PENDING" @selected(T = T)>
                                    PENDING
                                </option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Enter created_date</label>
                            <input type="date" name="created_at" id="created_at" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="button_action" id="button_action" value="insert" />
                        <input type="submit" name="submit" id="action" value="Add" class="btn btn-info" />
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            $('#transactionTable').DataTable();
        });
    </script>
    <script type="text/javascript">
        $('#add_data').click(function() {
            $('#transactionModal').modal('show');
            $('#transaction_form')[0].reset();
            $('#form_output').html('');
            $('#button_action').val('insert');
            $('#action').val('Add');
        });

        $('#transaction_form').on('submit', function(event) {
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url: "{{ route('postdata') }}",
                method: "POST",
                data: form_data,
                dataType: "json",
                success: function(data) {
                    if (data.error.length > 0) {
                        var error_html = '';
                        for (var count = 0; count < data.error.length; count++) {
                            error_html += '<div class="alert alert-danger">' + data.error[count] +
                                '</div>';
                        }
                        $('#form_output').html(error_html);
                    } else {
                        $('#form_output').html(data.success);
                        $('#transaction_form')[0].reset();
                        $('#action').val('Add');
                        $('.modal-title').text('Add Data');
                        $('#button_action').val('insert');
                        var mytbl = $("#transactionTable").Datatable();
                        mytbl.ajax.reload(null, false);


                    }
                }
            })
        });
    </script>
@endpush
