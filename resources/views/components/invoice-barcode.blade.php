<script>
    (function($) {
    var _timeoutHandler = 0,
        _inputString = '',
        _onKeypress = function(e) {
            if (_timeoutHandler) {
                clearTimeout(_timeoutHandler);
            }
            _inputString += e.key;

            _timeoutHandler = setTimeout(function () {
                if (_inputString.length <= 3) {
                    _inputString = '';
                    return;
                }
                $(e.target).trigger('altdeviceinput', _inputString);
                _inputString = '';

            }, 20);
        };
    $(document).on({
        keypress: _onKeypress
    });

    $(document).on("altdeviceinput", function (e, barCode) {

        motoroala_output = barCode.replace(/\\/g, '');

        var postData = "text";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
              type: "post",
              url: "/barcode/invoice",
              dataType:"json",
              data: "barcode="+motoroala_output+"&invoice={{$invoice->id}}",
              contentType: "application/x-www-form-urlencoded",
              success: function(response, textStatus, jqXHR) {
               
                  console.log(response);

                 
                  if(response.response == 'barcode-not-found'){
                    $("#modal-title").text('PRODUCT NOT FOUND');
                    $("#modal-message").text('Product not found, scan again or check your Inventory');
                    $('#modal').modal('show');
                  }

                  if(response.response == 'product-out-stock'){
                    $("#modal-title").text('PRODUCT OUT STOCK');
                    $("#modal-message").text('Product not Available, out of Stock');
                    $('#modal').modal('show');
                  }

                  if(response.response == 'new-transaction-created'){
                    document.location.reload(true);
                  }

              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log(errorThrown);
              }
          })


    });
})($);

</script>


<div class="modal" tabindex="-1" role="dialog" id="modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          
          <h5 class="modal-title text-danger" id="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="modal-message"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
