@extends('layouts.app')

@section('content')

      <div id="registrationRoutePage"></div>
      <p class="header-description">MobileID <label style="color: red; display: inline;"></label></p>
      <h1>Web to MobileID App</h1>
      <p> On this page you can use our  web application with backend to register a MobileID user account and enroll a new device. </p>

      <h2>Registration</h2>
      <h3><span>1 - Enter the <code>externalRef</code> and <code>deviceName</code></span></h3>

      <p><label>External reference</label></p><input id="externalRef" type="small-text-box"
        aria-label="External reference">
      <p><label>Device name</label></p><input id="deviceName" type="small-text-box" aria-label="Device name"><br>
      <h3><span>2 - Click the <b>Activate device</b> button</span></h3>

      <div><a tabindex="0" class="button" id="activate_device" onclick='doRegister()'>Activate device</a></div>

      <p><label>Activation code</label></p><input id="pairingCode" readonly="readonly" placeholder="Received code"
        aria-label="Activation code" type="small-text-box"><br>
        <input id="activated_status" readonly="readonly" value="" placeholder="Received code" aria-label="Activation code" type="hidden">

      <h3><span>3 - Use mobile app and enter activation code to activate MobileID on your device</span></h3>
      <p><label>Registration response</label></p><textarea id="registration_response" disabled="disabled"
        aria-label="Registration response"></textarea>


@endsection
@push('scripts')
<!-- ./wrapper -->
<script>
  var api_key = "{{ getenv('SECRET_API_KEY') }}";
  function doRegister() {
    var client_id = document.getElementById("externalRef").value;
    var client_name = document.getElementById("deviceName").value;
    var activated_device = document.getElementById("activated_status").value;
    var base_url = '{{ env('APP_URL') }}';
    $.ajax({
      type: 'POST',
      url: base_url + '/client',
      dataType: 'json',
      data: {
        "_token": "{{ csrf_token() }}",
        "client_id": client_id,
        "client_name": client_name,
        "activated_device": activated_device,
      },
      success: function (data) {
        if (data.is_activated == 1) {
          document.getElementById("activated_status").value = 1;
          var pairingCode = document.getElementById("pairingCode");
          pairingCode.value = data.activation_code;
          pairingCode.setAttribute("class", "activated");
          // 2nd ajax call
          checkStatus(client_id, data);
          //end ajax calll
        } else if (data.status == 'error') {
          document.getElementById("registration_response").innerHTML = JSON.stringify(data);
        } else {
          document.getElementById("pairingCode").value = data.activation_code;
            // 2nd ajax call
            checkStatus(client_id, data);
        }
      }
    });
  }


function checkStatus(client_id,data){
  var base_url = '{{ getenv('APP_URL') }}';
  const id = setInterval(function(){
          $.ajax({
                type: 'POST',
                url: base_url + '/api/register/checkStatus',
                dataType: 'json',
                headers: {
                "MobileIDAuthorization": api_key
                },
                data: {_token:"{{csrf_token()}}",client_id: client_id},
                success: function (data) {
                if(data.status==true) {
                document.getElementById("registration_response").innerHTML = JSON.stringify(data);
                clearInterval(id);
                }
                }
          });
          }, 3000);
          setTimeout(function(){
          clearInterval(id);
          }, 120000);
};
</script>
@endpush