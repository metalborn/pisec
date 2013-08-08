<?php

error_reporting(E_ALL);
ini_set('display_errors', True);

$shared_memory_id= 0;
$state = "";	

$shmobjpath = "/var/www/html/pisecSHM"; //must match with daemon shared memory objet path 

function readstate()
{   
    global $state, $shared_memory_id;
    
        $shmsize = shmop_size($shared_memory_id);
	//echo "Shared memory size: ".$shmsize." bytes <br />"; 
	$state = shmop_read($shared_memory_id, 0, $shmsize);				//Shared memory ID, Start Index, Number of bytes to read
	if($state == FALSE) 
	{
			echo "Failed to read shared memory";
			//sem_release($semaphore_id);
			exit;
	}
        
}

function printstate()
{
    global $state;
    
   	//Display as a string
	 echo "Shared memory string: " . $state . "<br />";      
        echo var_dump($state) . "<br />";     
	echo "Shared memory bytes: ";
	for($i = 0; $i < 10; $i++)
	{
		echo $state[$i] . ", ";
	}
    
}



function writestate()
{
     global $state, $shared_memory_id;

               // $datatowrite = implode($state);  
		//echo "Writing bytes: ". $datatowrite . "<br />";                
		shmop_write($shared_memory_id, $state, 0);  
}

function shmini()
{  
   global $shared_memory_id, $shmobjpath;
    $tries = 0;
    $openok = false;
    $SHARED_MEMORY_KEY = ftok("/var/www/html/pisecSHM", "a"); // 'a' = char(97), use any readable char, and it`s value in cpp, if you want project id in range 1 - 20, generate corresponding char (this is easier)  
    
   while ( ($tries < 10) && (!$openok)  )
    { 
        
       $shared_memory_id = shmop_open($SHARED_MEMORY_KEY, "w", 0, 17);   
        if ( empty($shared_memory_id) )
              { $tries+=1;} 
        else 
                { $openok = true; return true;  } 
    }

    if (empty($shared_memory_id))
    {
           return false;
    } 
    
}

function shmdeini()
{ global  $shared_memory_id;        
	//Detach from the shared memory        
        shmop_close($shared_memory_id);    
}



shmini();
$iniok =  true; //shmini();

if ($iniok )
{
 global $state;   
 readstate();
 echo $state;
 

}      
  shmdeini();


?>


