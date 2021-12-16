$(document).ready(function(){
    resetdata();
});
function resetdata() {
    $("#question_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#question').val("");
        $("#question_div").addClass("is-focused");

        $('#yes').prop("checked", true);
        $('#question').focus();
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(addtype=0) {
   
    var question = $('#question').val().trim();
    var isvalidquestion = 0;
   
    PNotify.removeAll();
    if(question=="") {
        $("#question_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter question !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else if(question.length < 2){
        $("#question_div").addClass("has-error is-focused");
        new PNotify({title: 'Question require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    } else {
        $("#question_div").removeClass("has-error is-focused");
        isvalidquestion = 1;
    }
    
    if(isvalidquestion ==1){
        var formData = new FormData($('#feedback-question-form')[0]);
        if(ACTION == 0){ // INSERT
            var baseurl = SITE_URL + 'feedback-question/feedback-question-add';
            $.ajax({
                
                url: baseurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    $("#question_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Feedback question successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(addtype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location = SITE_URL + "feedback-question";}, 500);
                        }
                    }else if(response==2){
                        new PNotify({title: 'Feedback question already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#question_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Feedback question not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
                 // MODIFY
            var baseurl = SITE_URL + 'feedback-question/update-feedback-question';
            $.ajax({
                url: baseurl,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    $("#question_div").removeClass("has-error is-focused");
                    if(response==1){
                        new PNotify({title: 'Feedback question successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location = SITE_URL + "feedback-question";}, 500);
                    }else if(response==2){
                        new PNotify({title: 'Feedback question already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#question_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Feedback question not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }        
    }
}
