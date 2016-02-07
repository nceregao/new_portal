$('#modal-delete')
    .modal({
        backdrop:true,
        keyboard: true,
        show: false
    })
    .on('show.bs.modal', function(e){
        var $this = $(this),
            data = e.relatedTarget;
        $this.find('#myModalLabel').text('Удалить '+data.name+'?');
        $this.find('#modal-delete-btn').data('news-id', data.id);
    })
    .on('click', '#modal-delete-btn', function(){
        var id = $(this).data('news-id');

        $.get('/document/remove', {id: id}, function(data){
            if ('OK' == data) {
                var el = $('.j-del[data-id='+id+']').closest('li');
                if ( !el.siblings().length ) return location.reload();
                else el.slideUp(function(){ el.remove() });
                $('#modal-delete').modal('hide');
            } else {
                alert('Error delete try again');
            }
        });
        return false;
    });

$('#documents-list').on('click', '.j-del', function(){
    $('#modal-delete').modal('show', {
        id: $(this).data('id'),
        name: $(this).parent().siblings('.j-document-name').text()
    });
    return false;
});

$('#select-category-add').change(function() {
    if ('other' == this.value) {
        $(this.form.newCategory).removeClass('hidden').fadeIn(50, function(){
            this.focus();
        });
    } else {
        $(this.form.newCategory).fadeOut();
    }
});




$('#modal-login')
    .modal({
        backdrop:true,
        keyboard: true,
        show: false
    })
    .on('shown.bs.modal', function(){
        $(this).find('input[name=name]').focus();
    });



$('#form-login').submit(function(){
    var _self = this;
    if (!this.name.value ) {
        this.name.focus();
        $(this).find('alert').text('Enter name').alert('show');
    } else if (!this.password.value) {
        this.password.focus();
        $(this).find('alert').text('Enter password').alert('show');
    } else {
        $(this.password).parent().removeClass('has-error');
        $(this.name).parent().removeClass('has-error');

        $.post('/account/login.php', {
            name: this.name.value,
            password: this.password.value
        }, function(data){
            if ('OK' == data) return location.reload();
            else {
                _self.password.value = '';
                var error_input = 'Invalid password' == data ? _self.password : _self.name;
                error_input.focus();
                $(error_input).parent().addClass('has-error');
                $(_self).find('.alert').text(data).alert('show');
            }
        });
    }
    return false;
});




$('#login-btn').on('click', '.j-del', function(){
    $('#modal-login').modal('show');
    return false;
});





$('#form-register').submit(function(e){
    var _self = this;
	if (!e.originalEvent) return;
    if (!this.name.value ) {
        this.name.focus();
        $(this).find('alert').text('Enter name').alert('show');
    } else if (!this.password.value) {
        this.password.focus();
        $(this).find('alert').text('Enter password').alert('show');
    } else {
        $(this.password).parent().removeClass('has-error');
        $(this.name).parent().removeClass('has-error');

        $.post('/account/register.php', {
            name: this.name.value,
            password: this.password.value
        }, function(data){
            if ('OK' == data) _self.submit();
            else {
                _self.password.value = '';
                var error_input = 'Invalid password.' == data ? _self.password : _self.name;
                error_input.focus();
                $(error_input).parent().addClass('has-error');
                $(_self).find('.alert').text(data).alert('show');
            }
        });
    }
    return false;
});