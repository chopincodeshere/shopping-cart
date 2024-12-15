<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <ul class="nav side-menu">
            @can('has-role', ['SUPER_ADMIN'])
                <li><a href={{ route('dashboard') }}> <i class="fa fa-home"></i> Dashboard</a></li>
            @endcan
            @can('has-role', ['SUPER_ADMIN'])
                <li><a href={{ route('users.index') }}> <i class="fa fa-user"></i> Users</a></li>
            @endcan

            <li><a href={{ route('products.index') }}> <i class="fa fa-user"></i> Products</a></li>

            @can('has-role', ['USER'])
                <li><a href={{ route('carts.index') }}> <i class="fa fa-user"></i> Your cart</a></li>
            @endcan

            @can('has-role', ['ADMIN', 'USER'])
                <li><a href={{ route('orders.index') }}> <i class="fa fa-user"></i> Orders</a></li>
            @endcan

        </ul>
    </div>

</div>
<!-- /sidebar menu -->
