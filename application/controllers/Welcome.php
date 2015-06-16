<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function index(){    
            $this->load->view('dir.php');
        }

        function greetings(){
            $array=$keywords = preg_split("/[\s,.:;?!]+/",$_GET['q']);
            $flag=0;
            for($i = 0; $i < count($array);$i++){
                if($array[$i] =="Hi" || $array[$i]=="hi" || $array[$i]=="Hello"||$array[$i]=="hello"){
                        //echo "Hello, Kitty! Nice to meet you. :)";
                   echo "<html><h2>".json_encode( array('answer' => "Hello, Kitty! Nice to meet you. :)"))."</h2></html>";
                   $flag=1;
                   break;
                }
                else if($i!=(count($array)-1) && ($array[$i]=="Good" ||$array[$i]=="good" )
                            && ($array[$i+1]=="Morning"||$array[$i+1]=="morning"
                            ||$array[$i+1]=="Evening"||$array[$i+1]=="evening"
                            ||$array[$i+1]=="Night"||$array[$i+1]=="night")){
                        //echo "Hello, Kitty! Thank you. Good ".$array[$i+1]. ":)";
                     echo "<html><h2>".json_encode( array('answer' => "Hello, Kitty! Thank you. Good ".$array[$i+1]. ":)"))."</h2></html>";
                     $flag=1;
                     break;
                }
             }
                 if($flag==0){
                     //echo "Hey Kitty! Are you saying something??";
                     echo "<html><h2>".json_encode( array('answer' => "Hey Kitty! Are you saying something??"))."</h2></html>";
                 }
         }

         function printInfo($info,$city){

            $jsonurl = "http://api.openweathermap.org/data/2.5/weather?q=".$city;
            $json = file_get_contents($jsonurl);
            $weather = json_decode($json);
            $assert= $weather->weather[0]->main;
            
            if($info=="temperature"){
                $kelvin = $weather->main->temp;
                $celcius = $kelvin - 273.15;
                echo "<html><h2>".json_encode( array('answer' => $celcius))."</h2></html>";
            }
            else if($info=="humidity"){
                $humid = $weather->main->humidity;
                echo "<html><h2>".json_encode( array('answer' => $humid))."</h2></html>";
            }
            else if($info=="clouds"){
                if($assert=="Clouds" || $assert=="clouds"){
                    echo "<html><h2>".json_encode( array('answer' => "Yes"))."</h2></html>";
                }
                else echo "<html><h2>".json_encode( array('answer' => "No"))."</h2></html>";
            }
            else if($info=="rain"){
                if($assert=="Rain"||$assert=="rain") echo "<html><h2>".json_encode( array('answer' => "Yes"))."</h2></html>";
                else echo "<html><h2>".json_encode( array('answer' => "No"))."</h2></html>";
            }
            else if($info=="clear weather"){
                if($assert=="Clear"||$assert=="clear") echo "<html><h2>".json_encode( array('answer' => "Yes"))."</h2></html>";
                else echo "<html><h2>".json_encode( array('answer' => "No"))."</h2></html>";
            }
         }

         function weather(){

            $array=$keywords = preg_split("/[\s,.:;?!]+/",$_GET['q']);
            $information="";
            $city="";
            for($i = 0; $i < count($array);$i++){
                if($array[$i]=="temperature"){
                    $information= "temperature";
                }
                else if($array[$i]=="humidity"){
                    $information="humidity";
                }
                else if($array[$i]=="Rain"||$array[$i]=="rain"){
                    $information="rain";
                }
                else if($array[$i]=="Clouds"||$array[$i]=="clouds"){
                    $information="clouds";
                }
                else if($array[$i]=="Clear"||$array[$i]=="clear"){
                    $information="clear weather";
                }
                else if($array[$i]=='in'){
                    for($k=$i+1;$k<count($array);$k++){
                       $city=$city.$array[$k];
                       if($k!=(count($array)-1)){
                           $city=$city."%20";
                       }
                    }
                    $this->printInfo($information,$city);  
                    break;
                }
            }
         }

         function qa(){
            $question=urlencode($_GET['q']);
            $jsonurl = "http://quepy.machinalis.com/engine/get_query?question=".$question;
            $json = file_get_contents($jsonurl);
            $raw=json_decode($json);
            $query=$raw->queries[0]->query;
            $target=trim($raw->queries[0]->target,"?");
            $query=urlencode($query);
            if($query==null){
               echo json_encode( array('answer' => "Your majesty! Jon Snow knows nothing! So do I!" ));
               return;
            }
            $url="http://dbpedia.org/sparql?query=".$query."&format=json";
            $json1=file_get_contents($url);
            $json1=json_decode($json1);
            $all= $json1->results->bindings;

            if(count($all)==0){
               echo json_encode( array('answer' => "Your majesty! Jon Snow knows nothing! So do I!" ));
               return;
            }

            if(count($all)==1){
                echo json_encode( array('answer' => $all[0]->$target->value ));
                return;
            }
            else{
                $str="xml:lang";
                foreach ($all as $row) {
                    if($row->$target->$str=="en"){
                        echo json_encode( array('answer' =>$row->$target->value ));
                        return;
                    }
                }
            }
            echo "<html><h2>". json_encode(array('answer' => $all[rand(0,(count($all)-1))]->$target->value))."</h2></html>";
            return;
         }
}
