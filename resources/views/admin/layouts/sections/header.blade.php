  <!-- Preloader -->
<!--   <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
     <!--  <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Home</a>
      </li> -->
    
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
 

       <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link"  href="{{ route('adminLogout') }}">
        <!-- <a class="nav-link" data-toggle="dropdown" href="{{ route('signout') }}"> -->
          <!-- <i class="far fa-user"></i> -->
          Logout
        </a>
       
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link text-center">
     Mobile ID 
    </a>

    <!-- Sidebar -->
    <div class="sidebar side-panel">
    
      <nav class="mt-2">
         <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{route('clientList')}}" class="nav-link {{ Request::segment(2) === 'client' ? 'active' : null }}">
              <p>
                Client
              </p>
            </a>
          </li> 
          <li class="nav-item">
            <a href="{{route('userList')}}" class="nav-link {{ Request::segment(2) === 'user' ? 'active' : null }}">
              <p>
                User
              </p>
            </a>
          </li> 
          <li class="nav-item">
            <a href="{{route('org.list')}}" class="nav-link {{ Request::segment(2) === 'organization' ? 'active' : null }}">
              <p>
                Organization
              </p>
            </a>
          </li> 
          <li class="nav-item">
            <a href="{{route('billing.create')}}" class="nav-link {{ Request::segment(2) === 'billing' ? 'active' : null }}">
              <p>
                Billing
              </p>
            </a>
          </li> 
          <li class="nav-item">
            <a href="{{route('cms.list')}}" class="nav-link {{ Request::segment(2) === 'cms' ? 'active' : null }}">
              <p>
                 App Cms
              </p>
            </a>
          </li> 

          <li class="nav-item">
            <a href="{{route('setting.list')}}" class="nav-link">
              <p>
                 Manage Setting
              </p>
            </a>
          </li> 
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

