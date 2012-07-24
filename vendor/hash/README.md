Instructions
=
1. Include the file: `hash/functions.php`
1. add a `do_action( 'hash-filter' );`
1. call the function in your javascript, example: 
`$( '#filter_task_form input[type=checkbox]' ).on( 'change', function(){    	
	    build_filters( _form_selector='#filter_task_form' );
});`
1. Theme as needed in your own CSS!
