var onHoldTable;
pos_app = {
    
    showAddProductModal : function(){
        $.ajax({
            dataType: 'json',
            type:'get',
            url : baseURL + 'simple_product/create_by_modal',
            success:function(res){
                $('#add_product_modal_con').html(res.view);
                $('#add_product_modal').modal('show');
                $('#file_2').ace_file_input({
                    no_file: 'No File ...',
                    btn_choose: 'Choose',
                    btn_change: 'Change',
                    droppable: false,
                    onchange: null,
                    thumbnail: false
                  });
            }
        });
    },
    saveProductWithForm : function(){
        $(".add-product-btn").LoadingOverlay("show");
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
            $(".add-product-btn").LoadingOverlay("hide");
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
                                  }
                              }
                          }
                      });
                  }
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $(".add-product-btn").LoadingOverlay("hide");
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
},
showOnHoldListModal : function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'sale/board/on_hold_modal_view',
        success:function(res){
            $('#on_hold_list_modal_con').html(res.view);
            $('#on_hold_list_modal').modal('show'); 


            onHoldTable = $('#yajra-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: baseURL + "sale/board/list?group_by=issue_key",
                columns: [
                    {
                        "data": "id",
                        title: 'Sr.',
                        width:'5%',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },{
                        data: 'last_update',
                        name: 'last_update',
                        title: 'Date Time',
                     
                    },{
                        data: 'item_count',
                        name: 'item_count',
                        title: 'Items Count',
                     
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action',
                        width:'100px',
                    }
                ],
                order: [[0, 'desc']]
            });


        }
    });
},
deleteOnHoldItem : function(saleKey){
    selectID = saleKey;
    $.confirm({
        title: 'Confirmation',
        content: 'Do you really want to delete ?',
        buttons: {
            yes: {
                text: 'Yes',
                action: function() {
                    $.ajax({
                        type: 'GET',
                        dataType: "JSON",
                        
                        url: baseURL+"sale/board/delete/board_item/"+selectID,
                        success: function(res, textStatus, jqXHR) {
                           
                            if (jqXHR.status == 200) {
                                onHoldTable.ajax.reload();
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
            },
            no: {
                text: 'No', // With spaces and symbols
                action: function() {

                }
            }
        }
    });
},
showStockRequestModal : function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'stock-exchange/create_req_view_modal',
        success:function(res){
            $('#create_customer_modal_con').html(res.view);
            $('#create_customer_modal').modal('show'); 
        }
    });
}


///////////////////////     Order Return      ////////////////////////////////////
, orderBackSearchModal : function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'sale/order/order_back_search_modal',
        success:function(res){
            $('#order_back_search_modal_con').html(res.view);
            $('#order_back_search_modal').modal('show'); 
        }
    });
},
submitSearchOrderForm : function(){
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
},
import_product_modal:function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'simple_product/import_products_modal',
        success:function(res){
            $('#import_product_modal_con').html(res.view);
            $('#import_product_modal').modal('show'); 
            $('#product_list_file').ace_file_input({
                no_file: 'No File ...',
                btn_choose: 'Choose',
                btn_change: 'Change',
                droppable: false,
                onchange: null,
                thumbnail: false
            });
        }
    });
}, 
saveImportProductsForm : function(){
    let myform = document.getElementById("import_products_form_model");
 let fdata = new FormData(myform);
 let actionURL = $('#import_products_form_model').attr('action');

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
},
//////===========================
showRoleAssignFormModal : function(id){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'usermanagement/user-role/modal-view-user-shop-role/'+id,
        success:function(res){
            $('#assign_role_modal_con').html(res.view);
            $('#assign_role_modal').modal('show'); 
        }
    });
},
saveUserRoleAssignForm : function(){
 let myform = document.getElementById("assign_users_roles_form");
 let fdata = new FormData(myform);
 let actionURL = $('#assign_users_roles_form').attr('action');

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
                                 $('#assign_role_modal').modal('hide');
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

// changeCurrentShop
changeCurrentShopModal : function(){
    $.ajax({
        dataType: 'json',
        type:'get',
        url : baseURL + 'usermanagement/user-role/change-user-current-shop',
        success:function(res){
            $('#change_user_current_shop_modal_con').html(res.view);
            $('#change_user_current_shop_modal').modal('show'); 
        }
    });
},
saveChangeShopForm : function(){
    let myform = document.getElementById("change_shop_form");
    let fdata = new FormData(myform);
    let actionURL = $('#change_shop_form').attr('action');
   
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
                    window.location.reload();
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


} // pos_app end