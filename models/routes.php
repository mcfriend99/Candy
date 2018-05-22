<?php

if(!defined('CANDY')){
	header('Location: /');
}

/*

Create Route Concepts here...

*/


// The :any wildcard can be appended to any route to mean that the route will still be the same no matter what follows it.
add_route(':index', 'welcome'); // This is the only reserved route. It means the home url. Same as route `^$`.



