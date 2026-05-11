<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Skote - Admin & Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body data-sidebar="dark">

<div id="layout-wrapper">

    {{-- TOPBAR --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">

                <div class="navbar-brand-box">
                    <a href="{{ route('dashboard') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-light.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="19">
                        </span>
                    </a>
                </div>

                <button type="button"
                        class="btn btn-sm px-3 font-size-16 header-item waves-effect"
                        id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search...">
                        <span class="bx bx-search-alt"></span>
                    </div>
                </form>

                <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
                    <button type="button"
                            class="btn header-item waves-effect"
                            data-bs-toggle="dropdown"
                            aria-haspopup="false"
                            aria-expanded="false">
                        <span>Mega Menu</span>
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                </div>

            </div>

            <div class="d-flex">

                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button"
                            class="btn header-item noti-icon waves-effect"
                            data-bs-toggle="fullscreen">
                        <i class="bx bx-fullscreen"></i>
                    </button>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button"
                            class="btn header-item noti-icon waves-effect">
                        <i class="bx bx-bell bx-tada"></i>
                        <span class="badge bg-danger rounded-pill">3</span>
                    </button>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button"
                            class="btn header-item waves-effect"
                            id="page-header-user-dropdown"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">

                        <img class="rounded-circle header-profile-user"
                             src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                             alt="Header Avatar">

                        <span class="d-none d-xl-inline-block ms-1">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>

                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bx bx-user font-size-16 align-middle me-1"></i>
                            Profile
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                        <i class="bx bx-cog bx-spin"></i>
                    </button>
                </div>

            </div>
        </div>
    </header>

    {{-- LEFT SIDEBAR --}}
    <div class="vertical-menu">
        <div data-simplebar class="h-100">

            <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">

                    <li class="menu-title">Menu</li>

                    <li>
                        <a href="{{ route('dashboard') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-title">User Management</li>

                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bxs-user-detail"></i>
                            <span>Users</span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('users.index') }}">Users List</a>
                            </li>

                            <li>
                                <a href="{{ route('users.create') }}">Create User</a>
                            </li>

                            <li>
                                <a href="{{ route('profile.edit') }}">Profile</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-group"></i>
                            <span>User Groups</span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('user-groups.index') }}">Group List</a>
                            </li>

                            <li>
                                <a href="{{ route('user-groups.create') }}">Create Group</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-title">Apps</li>

                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-store"></i>
                            <span>Ecommerce</span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('ecommerce.customers') }}">Customers</a>
                            </li>

                            <li>
                                <a href="{{ route('ecommerce.checkout') }}">Checkout</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-envelope"></i>
                            <span>Email</span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('email.basic') }}">Email Basic</a>
                            </li>

                            <li>
                                <a href="{{ route('email.billing') }}">Email Billing</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-title">Components</li>

                    <li>
                        <a href="{{ route('form.advanced') }}" class="waves-effect">
                            <i class="bx bxs-eraser"></i>
                            <span>Form Advanced</span>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @yield('content')

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © Skote.
                    </div>

                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Design & Develop by Themesbrand
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

<div class="rightbar-overlay"></div>

<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

@yield('script')

</body>
</html>