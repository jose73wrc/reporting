// Shorthand for $( document ).ready()
$(function() {
    console.log('ready');
  
    $( "body" ).click(function() {
        //const data = editor.getData();
        //console.log(data);
       // data = 'Hello World';
        //$('.section').append('<input name="data" type="hidden" value="'+data+'" />');
        console.log('clicked');
    });

    function sections(){
        var sec = ['Remarks','Analysis','Marketing','Sales-Operations','PresentForward'];

        for(var x in sec){
            $(".main").append('<div id="section_'+sec[x]+'" class="row section"><h4>'+sec[x]+'</h4></div>');
            $("div#section_"+sec[x]).append('<div class="text"></div>');
            $("div#section_"+sec[x]).append('<div class="tables"></div>');
        }                
    }
    
    function controls(){
       // $(".controls").append('<div class="btn-group" role="group"></div>');
       // $(".btn-group").append('<button type="button" class="btn btn-outline-primary">load</button>');
       // $(".btn-group").append('<button type="button" class="btn btn-outline-primary">save</button>');
       // $(".btn-group").append('<button type="button" class="btn btn-outline-primary">export</button>');
    }

    function init(){
        controls();
        //sections();

        /*$.ajax({
            url : '../reporting/reportingindex.php',
            type: 'GET',
            success : function(x){
                console.log(x);
                console.log('worked');
            }
        })*/
    }




    init();
  });