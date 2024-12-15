@extends('UI.base.main')
@section('content')
    <table id="carts_table" class="display">

    </table>
    <br>
    <div class="container">
        <form action="{{ route('orders.placeOrder') }}" method="post">
            @csrf
            <button class="btn btn-primary">Place Order</button>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let columns = [{
                        'data': "id",
                        "title": "Id"
                    }, {
                        "data": "product",
                        "title": "Image",
                        "render": function(data, type, row) {
                            if (data && data.product_images && data.product_images.length > 0) {
                                const imagePath = data.product_images[0].image_path; // Get the first image path
                                return `<img src="${imagePath}" alt="Product Image" style="max-width: 100px; max-height: 100px;" />`;
                            } else {
                                return 'No Image Available'; // Fallback for no images
                            }
                        }
                    }, {
                        "data": "product",
                        "title": "Name",
                        "render": function(data, type, row) {
                            return data ? data.name : 'N/A';
                        }
                    },
                    {
                        "data": "product",
                        "title": "Description",
                        "render": function(data, type, row) {
                            return data ? data.description : 'N/A';
                        }
                    },
                    {
                        "data": "quantity",
                        "title": "Quantity",
                    },
                    {
                        "data": "product",
                        "title": "Price",
                        "render": function(data, type, row) {
                            if (data && row.quantity) {
                                return `&#8377; ${data.price * row.quantity}`;
                            }
                            return 'N/A';
                        }
                    }, {
                        "title": "Actions",
                        'render': function(data, type, row) {
                            return `<button type="button" id="item-trash" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>`;
                        }
                    }

                ];

                var table = $('#carts_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 10,
                    "order": [], //no default sorting
                    "ajax": {
                        "url": "{{ route('carts.api') }}",
                        "type": "GET",
                        "dataFilter": function(json) {
                            let jsonData = jQuery.parseJSON(json);

                            updateTotals(jsonData.total_price, jsonData.tax, jsonData.grand_total);
                            jsonData.recordsTotal = jsonData.total;
                            jsonData.recordsFiltered = jsonData.total;
                            return JSON.stringify(jsonData);
                        },
                        "data": function(data) {
                            data.page = Math.floor(data.start / data.length) + 1;
                            data.per_page = data.length;
                            if (data.order.length > 0) {
                                data.sort_field = columns[data.order[0].column].data;
                                data.sort_order = data.order[0].dir;
                            }
                            if (data.search.value != '') {
                                data.search = data.search.value;
                            } else {
                                data.search = '';
                            }
                            return data;
                        }
                    },
                    "columns": columns,
                });

                function updateTotals(total, tax, grandTotal) {
                    $('#totals').html(`
                           <div class="container bg-white border rounded shadow p-4 w-100">
                                <h3 class="text-center mb-4">Payment Summary</h3>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-semibold">Total:</span>
                                    <span>&#8377; ${total}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-semibold">GST (18%):</span>
                                    <span>&#8377; ${tax}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between border-top pt-3 mt-3 fw-bold">
                                    <span>Grand Total:</span>
                                    <span>&#8377; ${grandTotal}</span>
                                </div>
                            </div>
                        `);
                }

                $('#carts_table_wrapper').after('<div id="totals" style="margin-top: 20px;"></div>');

                $('#carts_table').on('click', '#item-trash', function() {
                    const cartItemId = $(this).data('id');

                    $('#deleteCartForm input[name="cart_item_id"]').val(cartItemId);

                    $('#deleteCartModal').modal('show');
                });
            });
        </script>
    @endpush
@endsection
