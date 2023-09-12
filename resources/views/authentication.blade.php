@extends('layouts.app')
@section('content')


<div id="authenticationRoutePage"></div>

<p class="header-description">MobileID<label style="color: red; display: inline;"></label></p>
<h1>Web to MobileID App</h1>
<p> On this page you can authenticate a previously registered user. </p>
<h2>Authentication</h2>

<h3><span>1 - Enter the <code>externalRef</code> of the user you want to authenticate</span></h3>
<p><label>External reference</label></p>
<input id="externalRef" type="medium-text-box" value="" aria-label="External reference"><br>

<h3><span>2 - Click the <b>Get available devices</b> button and select an authentication device</span></h3>
<div><button class="button" id="activate_device" onclick='GetAvailableDevice()'>Get available devices</button></div>
<p><label>Available devices</label></p>
<select text="Choose a device to Authenticate" id="authentication-select" aria-label="Available devices" class="authentication-select signicat-select"></select><br><br>

<h3><span>3 - Optionally enter additional information to be passed back to the app</span></h3>
<p>
   <input type="checkbox" id="push_payload"><label for="pushPayloadCheck"> Specify push payload</label><!---->
</p>
<input type="text" id="push_payment_msg" class="medium-text-box" placeholder="Push message payload" style="display:none;">
<br><br>

<h3><span>4 - Click the <b>Authenticate</b> button</span></h3>
<div><button class="button" id="activate_device" onclick='doAuthenticate()'>Authenticate</button></div>
<br><br>

<h3><span>5 - Push notification is displayed on the mobile device. Carry out authentication</span></h3>
<p><label>Authentication response</label></p>
<textarea id="authenticateCompleteResponse" disabled="disabled" aria-label="Authentication response"></textarea>

@endsection
<!-- ./wrapper -->
@push('scripts')

<script>
  var api_key = "{{ getenv('SECRET_API_KEY') }}";
  function GetAvailableDevice() {
    var base_url = '{{ getenv('APP_URL') }}';
    var client_id = document.getElementById("externalRef").value;
    $.ajax({
      type: 'GET',
      url: base_url + '/client/' + client_id + '',
      dataType: 'json',
      success: function (data) {
        $("#authentication-select").attr('disabled', false);
        if (data.status == 'success') {
          $("#authentication-select").empty().append('<option value=' + data.data + ' selected>' + data.data +
            '</option>');
        } else {
          $("#authentication-select").empty();
        }
      }
    });
  }

  function doAuthenticate() {
    var client_id = document.getElementById("externalRef").value;
    var device = document.getElementById("authentication-select").value;
    var push_payload = document.getElementById("push_payment_msg").value;
    var base_url = '{{ getenv('APP_URL') }}';
    $.ajax({
      type: 'POST',
      url: base_url + '/create-token',
      dataType: 'json',
      data: {
        "_token": "{{ csrf_token() }}",
        "client_id": client_id,
        "device_id": device,
        "pushPayload":push_payload
      },
      success: function (data) {
        $("#authentication-select").attr('disabled', false);
        if (data.status == true) {
          $("#authentication-select").empty().append('<option value=' + data.data + ' selected>' + data.data +
            '</option>');
            // 2nd ajax call function
                checkStatus(client_id);
             // end 2nd ajax

        } else {
          document.getElementById("authenticateCompleteResponse").innerHTML = JSON.stringify(data);
          // $("#authentication-select").empty();
        }
      },
    error: function (jqXHR,status, err) {
        alert("Local error callback.");
    },
    complete: function (jqXHR,status) {
        console.log("Local completion callback.");
    }
    });
  }

 $("#push_payload").click(function () {
  if ($('#push_payload').is(":checked"))
    $("#push_payment_msg").show();
  else
    $("#push_payment_msg").hide();
 });

 function checkStatus(client_id){
  var base_url = '{{ getenv('APP_URL') }}';
  const id = setInterval(function(){
          $.ajax({
                type: 'POST',
                url: base_url + '/api/authenticate/checkStatus',
                dataType: 'json',
                headers: {
                "MobileIDAuthorization": api_key
                },
                data: {_token:"{{csrf_token()}}",client_id: client_id},
                success: function (data) {
                if(data.status==true) {
                document.getElementById("authenticateCompleteResponse").innerHTML = JSON.stringify(data);
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