/* ------------------------------------------------------------------------------
 *
 *  # Datatables API
 *
 *  Specific JS code additions for datatable_api.html page
 *
 *  Version: 1.0
 *  Latest update: Aug 1, 2015
 *
 * ---------------------------------------------------------------------------- */

$(function() {

    // Modal Dialog
    // ------------------------------

    /**
     * Edit Category
     */
    $('.btn_edit_category').on('click', function() {
        var category_id = $(this).data('value');
        $.ajax({
            type: "GET",
            url: './ajax/categories/get',
            data: {"category_id": category_id},
            dataType: 'json',
            success: function (data) {
                $('#category_id').val(data.category_id);
                $('#category_title').val(data.category_title);
                $('#price').val(data.price);
                $('#btn_category_submit').removeClass('btn-danger');
                $('#btn_category_submit').addClass('btn-success');
                $('#btn_category_submit').html('Update');
                $('#btn_category_submit').data('value', 'edit');
                $('#modal_category').modal('show');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    /**
     * Add Category
     */
    $('#btn_add_category').on('click', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "GET",
            url: './ajax/categories/next_id',
            dataType: 'json',
            success: function (data) {
                $('#category_id').val(data.category_id);
                $('#category_title').val('');
                $('#price').val('');
                $('#btn_category_submit').removeClass('btn-danger');
                $('#btn_category_submit').addClass('btn-success');
                $('#btn_category_submit').html('Create');
                $('#btn_category_submit').data('value', 'new');
                $('#modal_category').modal('show');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    /**
     * Delete Category
     */
    $('.btn_delete_category').click(function() {
        var category_id = $(this).data('value');
        $.ajax({
            type: "GET",
            url: './categories/ajax/categories/' + category_id,
            dataType: 'json',
            success: function (data) {
                $('#category_id').val(data.category_id);
                $('#category_title').val(data.category_title);
                $('#price').val(data.price);
                $('#btn_category_submit').removeClass('btn-success');
                $('#btn_category_submit').addClass('btn-danger');
                $('#btn_category_submit').html('Update');
                $('#btn_category_submit').data('value', 'delete');
                $('#modal_category').modal('show');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $('#modal_category').on('show.bs.modal', function() {
    });

    /**
     * Submit with Tag Dialog
     */
    $("#btn_category_submit").on("click", function(e){
        e.preventDefault();
        var slug = $('#modal_category form').attr('action') + "/" + $('#btn_category_submit').data('value');
        $('#modal_tag form').attr('action', slug).submit();
    });


    /**
     * Add Tags Dlg
     */
    $('#btn_add_tag').on('click', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "GET",
            url: './ajax/tags/next_id',
            dataType: 'json',
            success: function (data) {
                $('#tag_id').val(data.tag_id);
                $('#tag_name').val('');
                $('#btn_tag_submit').removeClass('btn-danger');
                $('#btn_tag_submit').addClass('btn-success');
                $('#btn_tag_submit').html('Add Tag');
                $('#btn_tag_submit').data('value', 'new');
                $('#modal_tag').modal('show');
            },
        });
    });

    /**
     * Edit Tags Dlg
     */
    $('.btn_edit_tag').on('click', function() {
        var tag_id = $(this).data('value');
        $.ajax({
            type: "GET",
            url: './ajax/tags/get',
            data: {"tag_id": tag_id},
            dataType: 'json',
            success: function (data) {
                $('#tag_id').val(data.tag_id);
                $('#tag_name').val(data.tag_name);
                $('#btn_tag_submit').removeClass('btn-danger');
                $('#btn_tag_submit').addClass('btn-success');
                $('#btn_tag_submit').html('Update');
                $('#btn_tag_submit').data('value', 'edit');
                $('#modal_tag').modal('show');
            }
        });
    });

    /**
     * Delete Tags Dlg
     */
    $('.btn_delete_tag').on('click', function() {
        var tag_id = $(this).data('value');
        $.ajax({
            type: "GET",
            url: './ajax/tags/get',
            data: {"tag_id": tag_id},
            dataType: 'json',
            success: function (data) {
                $('#tag_id').val(data.tag_id);
                $('#tag_name').val(data.tag_name);
                $('#btn_tag_submit').removeClass('btn-success');
                $('#btn_tag_submit').addClass('btn-danger');
                $('#btn_tag_submit').html('Delete');
                $('#btn_tag_submit').data('value', 'delete');
                $('#modal_tag').modal('show');
            }
        });
    });

    /**
     * Submit with Tag Dialog
     */
    $("#btn_tag_submit").on("click", function(e){
        e.preventDefault();
        var slug = $('#modal_tag form').attr('action') + "/" + $('#btn_tag_submit').data('value');
        $('#modal_tag form').attr('action', slug).submit();
    });

});




