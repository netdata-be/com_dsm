<?php

function dsmBuildRoute(&$query) {

       $segments = array();
       if( isset($query['task']) )
       {
                $segments[] = $query['task'];
                unset( $query['task'] );
       };
       if( isset($query['view']) )
       {
                //$segments[] = $query['view'];
                unset( $query['view'] );
       };
       if( isset($query['device']) )
       {
                unset( $query['device'] );
       };
       if( isset($query['template']) )
       {
                unset( $query['template'] );
       };
       if( isset($query['t']) )
       {
                $segments[] = $query['t'];
                unset( $query['t'] );
       };
	   
       return $segments;
	   
}


function dsmParseRoute( $segments ) {

       $vars = array();
	   //if(isset( $segments[0] )) {$vars['lang'] = $segments[0];}
	   if(isset( $segments[0] )) {$vars['task'] = $segments[0];}
	   if(isset( $segments[0] )) {$vars['view'] = $segments[0];}
	   if(isset( $segments[1] )) {$vars['t'] = $segments[1];}
	   
       return $vars;
	   
}


?>
