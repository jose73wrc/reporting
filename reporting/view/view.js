// Shorthand for $( document ).ready()
$(function() {
    console.log('ready');
  
    $( "body" ).click(function() {
        init();
        console.log('clicked');
    });
   
    
    function init(){     

        $.ajax({
            url : '../reporting/reportingindex.php',
            type: 'POST',
            data: { myData: 'This is my data.' }, 
            success : function(x){
                console.log(x);
                console.log('worked');
                $('body h1').css('color','red');
            }
        })
    }

    
});