pos_app = {
    
    showAddProductModal : function(){
         console.log('i am add product');
        // $('#add_product_modal').modal('show');
        $.ajax({
            dataType: 'json',
            type:'get',
            url : baseURL + 'simple_product/create_by_modal',
            success:function(res){

               // alert(res.view);

                $('#add_product_modal_con').html(res.view);

                $('#add_product_modal').modal('show');

                $('#file_2').ace_file_input({
                    no_file: 'No File ...',
                    btn_choose: 'Choose',
                    btn_change: 'Change',
                    droppable: false,
                    onchange: null,
                    thumbnail: false //| true | large
                    //whitelist:'gif|png|jpg|jpeg'
                    //blacklist:'exe|php'
                    //onchange:''
                    //
                  });
                
                console.log('yes',res);
                // if(res.status){
                //     table.ajax.reload();
                //     $('#statusPopup').modal('hide');
                // } 
            }
        });



    },
    saveProductWithForm : function(){
         console.log('i am add product');

  let myform = document.getElementById("product_form_model");
  let fdata = new FormData(myform);
  let actionURL = $('#product_form_model').attr('action');

      $.ajax({
          data: fdata,
          cache: false,
          processData: false,
          contentType: false,
          type: 'POST',
          dataType: "JSON",
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: actionURL,
          success: function(res, textStatus, jqXHR) {
              // console.log('=======>>>>> ',res);
              if (jqXHR.status == 200) {
                  if (typeof res.data.product !== 'undefined') {
                      $.confirm({
                          title: 'Success',
                          content: res.message,
                          buttons: {
                              yes: {
                                  text: 'OK',
                                  action: function() {
                                    getProductAttribute();
                                    $('#add_product_modal').modal('hide');
                                    
                                      // window.location = "{{ route('simple_product.create') }}";
                                  }
                              }
                          }
                      });
                  }
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
              // console.log(textStatus,jqXHR, errorThrown);
              if (jqXHR.status != 200) {
                  if (typeof jqXHR.responseJSON !== 'undefined') {
                      $.confirm({
                          title: 'Error',
                          content: jqXHR.responseJSON.message
                      });
                  }
              }
          }
      });
  




    } // function end
}