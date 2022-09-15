// Shorthand for $( document ).ready()
$(function() {
    console.log('ready');
  
    $( "body" ).click(function() {
        //const data = editor.getData();
        //console.log(data);
       // data = 'Hello World';
        //$('.section').append('<input name="data" type="hidden" value="'+data+'" />');
        init();
        console.log('clicked');
    });
   
    
    function init(){     

        $.ajax({
            url : '../reporting/js/reporting.js.php',
            type: 'POST',
            data: { myData: 'This is my data.' }, 
            success : function(x){
                console.log(x);
                console.log('worked');
            }
        })
    }

    
  });