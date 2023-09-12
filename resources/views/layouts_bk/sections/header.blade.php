@section('header')
<header class="signicat-header sticky">
  <div class="header-wrapper">
    <div class="menu-toggler"><i class="fas fa-bars"></i>
      <span>Menu</span>
    </div>
    <a class="logo"><img src="{{ asset('public/logo.png') }}" alt="Admin-DJ-Authmobileid" style="vertical-align: middle;"></a>
    <a class="nav-link"  href="{{ route('signout') }}" style='float:right;'>
          Logout
        </a>
    <nav id="navigator" class="">
      <h2>MobileID</h2>
     <?php if(strpos(Auth::user()->selected_option,"registration")!== false): ?> 
      <a href="{{ route('mobile-registration') }}" aria-current="page" class="{{ (request()->segment(1) == 'mobile-registration') ? 'router-link-exact-active active' : '' }}" id="registrationRoute">Registration</a><?php endif; ?>
      <?php if(strpos(Auth::user()->selected_option,"authentication")!== false): ?> 
      <a href="{{ route('authentication') }}" class="{{ (request()->segment(1) == 'authentication') ? 'router-link-exact-active active' : '' }}" id="authenticationRoute">Authentication</a><?php endif; ?>

      <?php if(strpos(Auth::user()->selected_option,"payment_authorization")!== false): ?> 
      <a href="{{ route('authorization') }}" class="{{ (request()->segment(1) == 'authorization') ? 'router-link-exact-active active' : '' }}" id="paymentRoute">Payment authorization</a><?php endif; ?>
      
      <?php if(strpos(Auth::user()->selected_option,"consent_signature")!== false):?> 
      <a href="{{ route('consent-sign') }}" class="{{ (request()->segment(1) == 'consent-sign') ? 'router-link-exact-active active' : '' }}" id="consentRoute">Consent signature</a>
      <?php endif;?>
    </nav>
  </div>
</header>
@endsection
@push('scripts')

<script>
  $(document).ready(function(){
    $(".menu-toggler").on('click',function () {
      $("#navigator").toggleClass('slide-in');
    });
  })
</script>
@endpush
<!-- /.content-wrapper -->
@push('scripts-footer')