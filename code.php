<?php
       
require_once("NAApiClient.php");
  
//variable de connexion
$config = array();
$config['client_id'] = "5517228c485a8894775f221c";
$config['client_secret'] = "Pz4PJr6pFJorbFRVcCHESEpaQd28v6ASmcYfJL7IMNH";
$client = new NAApiClient($config);
$username = "votre_identifiant";
$pwd = "votre_mot_de_passe";
$client->setVariable("username", $username);
$client->setVariable("password", $pwd);
  
//récupération du jeton de connexion
try
{
    $tokens = $client->getAccessToken();      
    $refresh_token = $tokens["refresh_token"];
    $access_token = $tokens["access_token"];
}
catch(NAClientException $ex)
{
    echo "An error happend while trying to retrieve your tokens\n";
}
   
//récupération des données de tous les sondes avec l'objet $deviceList
try
{
    $deviceList = $client->api("devicelist");  
      
    /* Tableau de données de toutes les sondes
    echo '<pre>' ;
    var_dump($deviceList["devices"]);
    echo '</pre>' ;
     */
       
    for ($i=0; $i <count($deviceList["devices"]) ; $i++) {
        if(isset($deviceList["devices"][$i])){
         
                    $device_id = $deviceList["devices"][$i]["_id"];
                    //récupération de la dernière valeur de la température et de l'humidité
                    $params = array("scale" =>"max",
                        "type"=>"Temperature,Humidity",
                        "date_end"=>"last",
                        "device_id"=>$device_id);
                    $res = $client->api("getmeasure", $params);
          
                    //tableau des mesures récupérer par les sondes
       /* echo '<pre>' ;
        var_dump( $res) ;
        echo '</pre>' ;*/
          
        if(isset($res[0]) &amp;&amp; isset($res[0]["beg_time"]))
        {
            $time = $res[0]["beg_time"];
            $t = $res[0]["value"][0][0];
            $h = $res[0]["value"][0][1];
            $emplacement = $deviceList["devices"][$i]["station_name"] ;
              
            echo utf8_decode($emplacement.'->'.$device_id." <br />Température : $t Celsius <br />");
            echo utf8_decode("Humidité is $h % <br /> Co<sup>2</sup> :".$deviceList["devices"][$i]["last_data_store"][$device_id]["h"]."<br />@".date('c', $time)." <br /><br />";
      
        }
    }
    }  
      
}
catch(NAClientException $ex)
{
    echo $ex."<br />User does not have any devices\n";
}     
      
?>
