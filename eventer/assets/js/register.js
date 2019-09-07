$(document).ready(function(){
    //On  click signup hide login, show registration
    $("#signup").click(function(){
        $("#first").slideUp("normal", function(){
            $("#second").slideDown("normal");
        });
    });
});
$(document).ready(function(){
    //On click signin hide register, show login
    $("#signin").click(function(){
        $("#second").slideUp("normal", function(){
            $("#first").slideDown("normal");
        });
    });
});