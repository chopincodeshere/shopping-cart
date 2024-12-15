<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $page_title ?? config('app.name') }}</title>
</head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/gentelella/1.3.0/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@yield('styles')
@stack('styles')

<body class="nav-md">

    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href={{ route('dashboard') }}
                            class="site_title"><span><b>{{ config('app.name') }}</b></span></a>
                    </div>

                    <div class="clearfix"></div>

                    @include('UI.base.sidebar')
                </div>
            </div>

            @include('UI.base.topbar')

            <!-- page content -->
            <div class="right_col" role="main">
                @yield('content')
            </div>

            <div class="modal fade" id="deleteCartModal" tabindex="-1" role="dialog"
                aria-labelledby="deleteCartModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCartModalLabel">Confirm Deletion</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to remove this product?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <form id="deleteCartForm" action="{{ route('carts.delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart_item_id"> <button type="submit"
                                    class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog"
                aria-labelledby="deleteProductModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteProductModalLabel">Confirm Deletion</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this product?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <form id="deleteProductForm" action="{{ route('products.delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id"> <button type="submit"
                                    class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="uploadImagesModal" tabindex="-1" role="dialog"
                aria-labelledby="addToCartModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form id="uploadImagesForm" action="{{ route('products.upload-images') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addToCartModalLabel">Upload product images</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <input type="number" required hidden name="product_id">
                                    <label for="image">Images:</label>
                                    <input type="file" multiple class="form-control" id="image" name="image[]"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="addToCartSubmit">Upload</button>
                            </div>
                    </form>
                </div>
            </div>

            
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
@stack('scripts')

</html>
