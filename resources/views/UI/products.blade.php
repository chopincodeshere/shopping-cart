@extends('UI.base.main')
@section('content')
    @can('has-role', ['ADMIN'])
        <div class="container w-100">
            <form action="{{ route('products.import') }}" method="post" class="d-flex" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <input type="file" name="import_file" class="form-control" id="inputGroupFile04"
                        aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                </div>
                <button class="btn btn-primary" type="submit" id="inputGroupFileAddon04">Upload</button>
            </form>
        </div>
    @endcan

    <table id="products_table" class="display">

    </table>

    <div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addToCartForm" action="{{ route('carts.add') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <input type="number" required hidden name="product_id">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addToCartSubmit">Add to Cart</button>
                    </div>
            </form>
        </div>
    </div>
  
    @push('scripts')
        <script>
            $(document).ready(function() {
                let canAddToCart = @json($canAddToCart);
                let canDeleteProduct = @json($canDeleteProduct);

                let columns = [{
                        'data': "id",
                        "title": "Id"
                    }, {
                        "data": "product_images",
                        "title": "Image",
                        "render": function(data, type, row) {
                            if (data && data.length > 0 && data[0].image_path) {
                                return `<img src="${data[0].image_path}" alt="Product Image" style="max-width: 100px; max-height: 100px;" />`;
                            } else {
                                return 'No Image Available';
                            }
                        }
                    }, {
                        "data": "name",
                        "title": "Name"
                    },
                    {
                        "data": "description",
                        "title": "Description",
                    },
                    {
                        "data": "stock",
                        "title": "Stock",
                    },
                    {
                        "data": "price",
                        "title": "Price",
                        "render": function(data, type, row) {
                            return `&#8377; ${data}`;
                        }
                    },
                ];

                if (canAddToCart) {
                    columns.push({
                        "title": "Actions",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            return `<button class="btn btn-success" id="add-to-cart" data-id="${row.id}">Add to cart</button>`;
                        }
                    });
                }

                if (canDeleteProduct) {
                    columns.push({
                        "title": "Actions",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            return `<button class="btn btn-danger" id="delete-product" data-id="${row.id}">Delete</button> &nbsp; <button data-id="${row.id}" type="button" class="btn btn-outline-primary" id="upload-images">Upload Images</button>`;
                        }
                    });
                }

                var table = $('#products_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 10,
                    "order": [], //no default sorting
                    "ajax": {
                        "url": "{{ route('products.api') }}",
                        "type": "GET",
                        "dataFilter": function(json) {
                            let jsonData = jQuery.parseJSON(json);
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
                

                $('#products_table').on('click', '#add-to-cart', function() {
                    const productId = $(this).data('id');
                    const productName = $(this).data('name');
                    const productPrice = $(this).data('price');

                    $('#addToCartForm input[name="product_id"]').val(productId);

                    $('#addToCartModal').modal('show');
                });

                $('#products_table').on('click', '#delete-product', function() {
                    const productId = $(this).data('id');

                    $('#deleteProductForm input[name="product_id"]').val(productId);

                    $('#deleteProductModal').modal('show');
                });

                $('#products_table').on('click', '#upload-images', function() {
                    const productId = $(this).data('id');

                    $('#uploadImagesForm input[name="product_id"]').val(productId);

                    $('#uploadImagesModal').modal('show');
                });
            });
        </script>
    @endpush
@endsection
