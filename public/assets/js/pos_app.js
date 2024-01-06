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
    }, // function end
    showChangePasswordModal : function(userID){
       $.ajax({
           dataType: 'json',
           type:'get',
           url : baseURL + 'usermanagement/user/change_password_modal_view/'+userID,
           success:function(res){
               $('#update_password_modal_con').html(res.view);
               $('#update_password_modal').modal('show'); 
           }
       });
   },
   savePasswordForm : function(){
    let myform = document.getElementById("change_password_form");
    let fdata = new FormData(myform);
    let actionURL = $('#change_password_form').attr('action');
  
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
                if (jqXHR.status == 200) {
                    $.confirm({
                        title: 'Success',
                        content: res.message,
                        buttons: {
                            yes: {
                                text: 'OK',
                                action: function() {
                                    $('#update_password_modal').modal('hide');
                                }
                            }
                        }
                    });
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
   },

   showCreateCustomerModal : function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'customer/create_view_modal',
        success:function(res){
            $('#create_customer_modal_con').html(res.view);
            $('#create_customer_modal').modal('show'); 
        }
    });
},
saveCustomerForm : function(){
 let myform = document.getElementById("create_customer_form");
 let fdata = new FormData(myform);
 let actionURL = $('#create_customer_form').attr('action');

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
             if (jqXHR.status == 200) {
                 $.confirm({
                     title: 'Success',
                     content: res.message,
                     buttons: {
                         yes: {
                             text: 'OK',
                             action: function() {
                                 $('#create_customer_modal').modal('hide');
                             }
                         }
                     }
                 });
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
}

} // pos_app end