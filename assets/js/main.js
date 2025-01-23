/* Authorization */

$(document).ready ( function(){
    
       $.ajax({
        url: '?login',
        type: 'POST',
        dataType: 'json',
        
        data: {
            
        },

        success (data) {
            if (data.status && data.type === 0){
                console.log('hi');
                //$('.msg-token').removeClass('none').text(data.message);
                $('.msg-token').removeClass('none').html(data.message);
                
            }
        }
    });   
  
});



$('.login-btn').click(function (e) {

    e.preventDefault();

    $(`input`).removeClass('error');
    
    let email = $('input[name="email"]').val(),
        password = $('input[name="password"]').val();

    $.ajax({
       url: '?login',
        type: 'POST',
        dataType: 'json',
        data: {
            email: email,
            password: password
        },

        success (data) {
            if (data.status){
                document.location.href = 'views/profile.php';
            } else {
                if (data.type === 1){
                    data.fields.forEach(function (field) {
                        $(`input[name="${field}"]`).addClass('error');
                    })
                }
                $('.msg').removeClass('none').text(data.message);
            }            
        }
    });

    
});




/* Registration */


$('.register-btn').click(function (e) {

    e.preventDefault();

    $(`input`).removeClass('error');
    
    let login = $('input[name="login"]').val(),
        email = $('input[name="email"]').val(),
        password = $('input[name="password"]').val(),
        password_confirm = $('input[name="password_confirm"]').val();

    
    let formData= new FormData();
        formData.append('login', login);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('password_confirm', password_confirm);
        
    
        $.ajax({
        url: '?signup',
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
       
        data: formData,

        success (data) {
            console.log("step: 1");
            if (data.status===true){
               // console.log(data);
                $('.msg').removeClass('none').text(data.message);
               
              document.location.href = '?login';
            } else {
                if (data.type === 1){
                    data.fields.forEach(function (field) {
                        $(`input[name="${field}"]`).addClass('error');
                    });
                }
                $('.msg').removeClass('none').text(data.message);
               
             }            
        }
    });

    
});

/*Recovery password Part1 */

$('.recovery-btn').click(function (e) {

    e.preventDefault();

    $(`input`).removeClass('error');
    
    let email = $('input[name="email"]').val();
        
    $.ajax({
        url: '?recovery_one',
        type: 'POST',
        dataType: 'json',
        data: {
            email: email
        },

        success (data) {
            if (data.status){
                document.location.href = 'views/profile.php';
            } else {
                if (data.type === 1){
                    data.fields.forEach(function (field) {
                        $(`input[name="${field}"]`).addClass('error');
                    })
                }
                $('.msg').removeClass('none').html(data.message);
            }            
        }
    });

    
});

/*Recovery password Part2 */
$('.recovery-two-btn').click(function (e) {

    e.preventDefault();

    $(`input`).removeClass('error');
    
    let pass1 = $('input[name="pass1"]').val(),
        pass2 = $('input[name="pass2"]').val();
        
    $.ajax({
        url: '?recovery_two_set_new_pass',
        type: 'POST',
        dataType: 'json',
        data: {
            pass1: pass1,
            pass2: pass2
        },

        success (data) {
            if (data.status){
                document.location.href = 'views/profile.php';
            } else {
                if (data.type === 1){
                    data.fields.forEach(function (field) {
                        $(`input[name="${field}"]`).addClass('error');
                    })
                }
                $('.msg').removeClass('none').html(data.message);
            }            
        }
    });

    
});