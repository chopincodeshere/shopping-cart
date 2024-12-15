<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        @if (auth()->check())
                            <img src="{{ auth()->user()->profile_image ? Storage::url(auth()->user()->profile_image) : asset('images/user.png') }}"
                                alt="">
                            {{ auth()->user()->name }}
                        @else
                            <img src="{{ asset('images/user.png') }}" alt="">
                            Guest
                        @endif
                        <span class="fa fa-angle-down"></span>
                    </a>

                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        @if (auth()->check())
                            <li>
                                <a href="javascript:;" onclick="logout()">
                                    <i class="fa fa-sign-out pull-right"></i> Log Out
                                </a>
                            </li>
                            <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="display: none;"></button>
                            </form>
                        @else
                            <li>
                                <a href="{{ route('login') }}">
                                    <i class="fa fa-sign-in pull-right"></i> Login
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->
<script>
    function logout() {
        document.getElementById('logoutForm').submit();
    }
</script>
