function getItem(id){
    $('#item_id').text(id);
}
function getReportDay(id){
    $('#reportday_id').text(id);
    console.log(id)
}
$(function () {
    $('#delete_btn_categorie').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletecategorie,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item':$('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
$('#delete_btn').click(function () {
    $.ajax({
        url: configs.routes.ajaxdeleteperiode,
        type: "GET",
        dataType: "JSON",
        data: {
          'item':$('#periode_id').text()
        },
        success: function (data) {
            window.location.reload(true);
        },
        error: function (err) {
            alert("An error ocurred while loading data ...");
        }
    });
})
    $('#delete_btn_user').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeleteuser,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item':$('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_caisse').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletecaisse,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item':$('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_soin').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletesoin,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item':$('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_typesoin').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletetypesoin,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item':$('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_conge').click(function () {
        $.ajax({
            url: configs.routes.ajaxdeleteconge,
            type: "GET",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_product').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeleteproduct,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_customer').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletecustomer,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text(),
                'action': 1
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#delete_btn_cmd_fournisseur').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletecmdefournisseur,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text(),
                'action': 1
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#activate_btn_customer').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletecustomer,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text(),
                'action': 0
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })

    $('#delete_btn_fournisseur').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: configs.routes.ajaxdeletefournisseur,
            type: "DELETE",
            dataType: "JSON",
            data: {
                'item': $('#item_id').text()
            },
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    })
    $('#confirm_btn_calandar').click(function () {
        $.ajax({
            url: configs.routes.reportcalandar,
            type: "GET",
            dataType: "JSON",
            data: {
                'item':$('#reportday_id').text()
            },
            success: function (data) {
                console.log(data)
                if (data.status===200){
                    window.location.reload(true);
                }else {
                    alert(data.data);
                }

            },
            error: function (err) {
                console.log(err)
                alert("An error ocurred while loading data ...");
                //window.location.reload(true);
            }
        });
    })
    $('#selectAll').click(function(e){
        var table= $(e.target).closest('table');
        $('td input:checkbox',table).prop('checked',this.checked);
    });
    $('#send_mail').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        jsonObj = [];
        $("#table_user>tbody input[type=checkbox]:checked").each(function () {
            var row = $(this).closest('tr')[0];
            var id = row.cells[1].innerText;
            item = {};
            item['id'] = id;
            jsonObj.push(item)
        });
        console.log(JSON.stringify({data: jsonObj}))
        $.ajax({
            url: configs.routes.sendmail,
            type: "POST",
            dataType: "JSON",
            data: JSON.stringify({
                ob: jsonObj,subject:$('#subject').val(),message:$('#message_mail').val()
            }),
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                console.log(err)
                alert("An error ocurred while loading data ..."+err.responseJSON.message);
            }
        });
    })
    $('#add_line').click(function () {
        $('#status_accessoire').text('')
        var qte = $('#quantity').val();
        var id = $('#facture_prod_id option:selected').val();
        var libelle = $('#facture_prod_id option:selected').text();
        var price = $('#facture_prod_id option:selected').data('price');
        var idtd = "line_" + id;
           $("#table_product>tbody:last").append("<tr id='" + idtd + "'><td>" +
                "<input class='checkbox hidden' type='checkbox' checked><span class='hidden' hidden>" + id + "</span></td>" +
                "<td>" + libelle + "</td><td>" + price + "</td><td>" + qte + "</td><td>" + (qte*price) + "</td><td>" + (qte*price*0.21) + "</td><td>" + (qte*price+(qte*price*0.21)) + "</td><td><a onclick='removeRow(" + id + ")' class='btn btn-sm btn-danger'>Del</a></td></tr>");

    })
    $('#add_line_fournisseur').click(function () {
        var qte = $('#quantity').val();
        var id = $('#product_fournisseur option:selected').val();
        var libelle = $('#product_fournisseur option:selected').text();
        var price = $('#product_fournisseur option:selected').data('price');
        var idtd = "line_" + id;
        $("#table_line_fournisseur>tbody:last").append("<tr id='" + idtd + "'><td>" +
            "<input class='checkbox hidden' type='checkbox' checked><span class='hidden' hidden>" + id + "</span></td>" +
            "<td>" + libelle + "</td><td>" + price + "</td><td>" + qte + "</td><td>" + (qte*price) + "</td><td><a onclick='removeRow(" + id + ")' class='btn btn-sm btn-danger'>Del</a></td></tr>");

    })
    $('#btn-save').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        jsonObj = [];
        $("#table_product>tbody input[type=checkbox]:checked").each(function () {
            var row = $(this).closest('tr')[0];
            var id = row.cells[0].children[1].innerText;
            var quantity = row.cells[3].innerText;
            item = {};
            item['quantity'] = quantity;
            item['id'] = id;
            jsonObj.push(item)
        });
        console.log(JSON.stringify({data: jsonObj}))
        $.ajax({
            url: configs.routes.ajaxafacturation_create,
            type: "POST",
            dataType: "JSON",
            data: JSON.stringify({
                ob: jsonObj, customer_id: $('#customer_id option:selected').val(),
            }),
            success: function (data) {
                console.log(data)
                window.location.reload(true);

            },
            error: function (err) {
                alert("An error ocurred while loading data ...");

            }
        });
    })
    $('#fournissueur_select').change(function () {
        $.ajax({
            url: configs.routes.getproduits,
            type: "GET",
            dataType: "JSON",
            data: {
                'item':$(this).val()
            },
            success: function (data) {
                console.log(data)
                $('#product_fournisseur').html('')
                $.each(data.data,function (index,value) {
                    $('#product_fournisseur').append('<option data-price="'+value.price+'" value="'+value.id+'">'+value.libelle+'</option>')
                })
            },
            error: function (err) {
                console.log(err)
                alert("An error ocurred while loading data ...");
                //window.location.reload(true);
            }
        });
    })
    $('#btn_cmde_save').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        jsonObj = [];
        $("#table_line_fournisseur>tbody input[type=checkbox]:checked").each(function () {
            var row = $(this).closest('tr')[0];
            var id = row.cells[0].children[1].innerText;
            var quantite = row.cells[3].innerText;
            item = {};
            item['id'] = id;
            item['quantite'] = quantite;
            jsonObj.push(item)
        });
        console.log(JSON.stringify({data: jsonObj}))
        $.ajax({
            url: configs.routes.saveline,
            type: "POST",
            dataType: "JSON",
            data: JSON.stringify({
                ob: jsonObj,fournisseur:$('#fournissueur_select').val()
            }),
            success: function (data) {
                window.location.reload(true);
            },
            error: function (err) {
                console.log(err)
                alert("An error ocurred while loading data ..."+err.responseJSON.message);
            }
        });
    })
})
function removeRow(id) {
    line = "#line_" + id;
    $(line).remove();
}
