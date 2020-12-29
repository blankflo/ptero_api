<?php
namespace App\Classes;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use App\Models\Survey;

class Server_ptero
{
    protected $user;//Instance de user
    protected $uri;
    protected $endpoint;
    protected $apikey;
    public static $count_server;
    private $created=5120;
    private $id_allocation;
    private $node_id;
    


    public function __construct(User $user, String $uri, $apikey, String $endpoint="api/application/")
    {
        $this->user = $user;
        $this->uri = $uri;
        $this->apikey = $apikey;
        $this->endpoint = $endpoint;
        self::$count_server++;
        $this->UpdateSurvey();
        
    }

    private function verifyStatusCode($response, int $code_valid){ //ok

        try{
        if($response->getStatusCode() !== $code_valid || $response->getBody()==null){
           
            return false; }
         else {return true; }    
           

    }catch(Exeption $e){ return false ;}
        
    }
    private function UpdateSurvey(){

        Survey::insert([
            "servers_started"=>$this->created,
            "servers_created"=>self::$count_server,
            "allocations_remaining"=>$this->allocation_remaining(),
            "created_at"=>Carbon::now()
        ]);

    }

    public function getUserIdPtero(){
        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."users"); // ok
         
                
          //  dump($response);

                foreach($response['data'] as $obj){
                  
                  if($obj["attributes"]["email"] == $this->user->email){
                      return$obj["attributes"]["id"]; 
                  };
            }
        }
            catch(Exeption $e){
    
                return false;
            } 
        
            return false;
        }

    
    public function getInfoServer(String $external_id){ //ok

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."servers/external/".$external_id);
                                
                //$this->verifyStatusCode($response, 201);
                dump(json_decode($response->getBody()));

                if (!$this->verifyStatusCode($response, 200)){return false;}

            // $response = json_decode($response->getBody());
                $response = json_decode($response->getBody());
            return $response;
        
        }
    
            catch(Exeption $e){
    
                return false;
            } 
        
           
        }

            
    public function getIdserver(String $external_id){ //ok

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."servers/external/".$external_id);
                                
                //$this->verifyStatusCode($response, 201);
                dump(json_decode($response->getBody()));

                if (!$this->verifyStatusCode($response, 200)){return false;}
            $response = json_decode($response->getBody());
            // $response = json_decode($response->getBody());
                foreach($response->attributes as $rep=>$val){
          
                    if($rep == "id"){
                        return $val;
                    }
   

                }
        
        }
    
            catch(Exeption $e){
    
                return false;
            } 
                return false;
           
        }
        public function getidentifier(String $external_id){ //ok

            try{
                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."servers/external/".$external_id);
                                    
                    //$this->verifyStatusCode($response, 201);
                    dump(json_decode($response->getBody()));
    
                    if (!$this->verifyStatusCode($response, 200)){return false;}
    
                // $response = json_decode($response->getBody());
                $response = json_decode($response->getBody());
                // $response = json_decode($response->getBody());
                    foreach($response->attributes as $rep=>$val){
              
                        if($rep == "identifier"){
                            return $val;
                        }
       
    
                    }
            
            }
        
                catch(Exeption $e){
        
                    return false;
                } 
                    return false;
               
            }



    //debut server 
    // public function getUserIdPtero(){

    //     $email =$this->user->email;

    //     try{
    //         $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint."/users",[
    //             "email"=>$email    
    //         ]);
       
    //             $this->verifyStatusCode($response, 201);

    //             foreach($response['data'] as $obj){
    //                 return $obj["attributes"]["id"];
    //         }
    //     }
    //         catch(Exeption $e){
    
    //             return false;
    //         } 
        
    //         return false;
    //     }
        // public function getIdServers($external_id){

        //     $email =$this->user->email;
    
        //     try{
        //         $response = Http::timeout(400)->withToken($this->apikey)->post($this->uri.$this->endpoint."server/external/".$external_id);
                                    
        //             $this->verifyStatusCode($response, 201);
    
        //             foreach($response['data'] as $obj){
        //                 return $obj["attributes"]["id"];
        //         }
        //     }
        //         catch(Exeption $e){
        
        //             return false;
        //         } 
            
        //         return false;
            // }
    


    public function createServer(String $name ,  $model, String $games, String $external_id){ //to finish
//tested
$environment=[];
    try{
        // $games =strtoupper($games);
        switch($games){
            case "garrysmod":
                $environment=["SRCDS_MAP" => "gm_flatgrass",
                                "SRCDS_APPID"=>"4020",             
                                 "GAMEMODE" => "sandbox",
                                     "TICKRATE"=>22,
                                        "MAX_PLAYERS"=>$model->max_players];
            break;
                        
                                        
                                            
             case "minecraft":
                          $environment=["BUNGEE_VERSION"=> "latest",
                                         "SERVER_JARFILE"=>"server.jar"]; 
             break;
            //  default:
            //     throw new Exception('bad games used');   
           
        }


       $id_allocation=$this->id_allocation_available();
            // if(isset($option["external_id"])&&isset($option["name"])&&isset($option["egg"])&&isset($option["docker_img"])&&isset($option["startup"])&&isset($option["environment"])&&isset($option["limits"])&&isset($option["features_limits"])&&isset($option["allocations"])){
            $encode = [
                "external_id"=>$external_id,
                "name"=>$name,
                "user"=>$this->getUserIdPtero(),
                "egg"=>$model->egg,
                
                "docker_image"=>$model->docker_img,
                "startup" =>$model->startup,
                // "auto_deploy"=>true,

                "environment"=>$environment,
                "limits"=>[                
                    "memory"=>$model->ram, 
                    "swap"=>$model->swap, 
                    "disk"=>$model->disk, 
                    "io"=>$model->io, 
                    "cpu"=>$model->cpu
                     ],
                "feature_limits"=>["databases"=> $model->database,
                "backups"=> $model->backup],
                "allocation"=>["default"=>$id_allocation]
            ];
                  
             //    ];}}
        
            // dump($this->listEgg());
          
        
              
            $response = Http::timeout(30)->withToken($this->apikey)->post("https://app.nexifi.games/api/application/servers",$encode);
        
           if($this->verifyStatusCode($response, 201)){
            $this->created++;
              
                return true;

           }
            

                  
                }
                catch(Exeption $e)
                {
                    return false;
                }
                
            return false;

    }

    
    public function actionServerPtero(String $external_id, String $method) //to test
    {
        $methods = "";
        try{
            if($method !== "suspend" || $method !== "unuspend"|| $method !== "reinstall"){
                
                throw new Exeption("bad method use");
            }
            $methods = "/".$method;

        $response = Http::timeout(400)->withToken($this->apikey)->post($this->uri.$this->endpoint."server/".$this->getIdServer($external_id).$methods);

        if (!$this->verifyStatusCode($response, 204)){return false;}

        }
        catch(Exeption $e){

            return false;
        } 
        return true;     
    }
    
    public function deleteServer(String $external_id){ //tested
        if(isset($external_id)){
        // $force = "force";
        // $force = ($force) ?: '';

        $id_server;

        if($this->getInfoServer($external_id)!== false){$id_server = $this->getInfoServer($external_id);
       
            foreach($id_server->attributes as $ia=>$val){
                if($ia == "id"){
                    $id_server = $val;
                }
 

            }}
        else{return false;}
    
        try{
            $response = Http::timeout(400)->withToken($this->apikey)->DELETE($this->uri.$this->endpoint."servers/".$id_server);    
            $code = $response->getStatusCode();

            if($code!==204){
                return false;
            }
            
               
            }
            catch(Exeption $e){
    
                return false;
            } 
    }
        else{throw new Execption("pls check every parameters") ;}
        return true;     

    }
    public function ModifServer(bool $build=false, $details=false, bool $startup=false ,String $external_id, Array $option  ){ //finish

        try{            
                $details_verif = "/details";
                $build_verif = "/build";
                $startup_verif = "/startup";
                $req=[];

                if($build){
                    if(!is_null($option['external_id']) || !is_null($option['allocations']) || !is_null($option['limits']) || !is_null($option['features_limits'])){
                        throw new Exeption('left paramters build');
                    }
                }
                elseif($details){
                    if(!is_null($option['name']) || !is_null($option['external_id']) || !is_null($option['description'])){
                        throw new Exeption('left paramters details');
                    }
                }
                elseif($startup){
                    if(!is_null($option['environment']) || !is_null($option['egg']) || !is_null($option['startup']) || !is_null($option['docker_image'])){
                        throw new Exeption('left paramters build');
                    }
                
                }
                else{
                    throw new Exeption('method dont choose');
                }
                
        
                if($build){$verif=1;$method=$startup_verif;
                    
                $req=["allocation"=> $option["allocation"], "memory"=>$option['limits']['memory'], "swap"=> $option['limits']['swap'], "disk"=> $option['limits']['disk'],"io"=> $option['limits']["io"],
                    "cpu"=>$option['limits']["cpu"],"threads"=> null,"feature_limits"=> ["databases"=> $option['features_limits']['databases'],"allocations"=> $option['features_limits']['allocation'],"backups"=> $option['backups']]];}
                elseif($details  && !$verif){$verif=1;$method=$details_verif;$req=["name"=> $option['name'], "user"=>$this->getUserIdPtero(), "external_id"=>$option['external_id'], "description"=> $option['description']];}
                elseif($startup && !$verif){$method=$build_verif;$verif=1;$req=["startup"=> $option['startup'],"environment"=>$option['environment'], "egg"=>$option['egg'], "image"=>$option['image'],"skip_scripts"=>false];}

                $response = Http::timeout(400)->withToken($this->apikey)->patch($this->uri.$this->endpoint."server/".$this->getIdServer($external_id).$method, $req);

               if($response->getBody() == null){throw new Exeption('invalid response');}
            }
            catch(Exeption $e){
    
                return false;
            } 
        return true;     

    }

    
    public function listEgg(){ //work

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri."api/application/nests/1/eggs?include=nest,servers");
                                
                //$this->verifyStatusCode($response, 201);
                if ($this->verifyStatusCode($response, 200)){return false;}
               return $response;
                 
        }
            catch(Exeption $e){
    
                return false;
            } 
        
            return false;
        }


            
    public function list_server(){ //work

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri."api/application/servers");
                                
                //$this->verifyStatusCode($response, 201);
                                    dump(json_decode($response->getBody()));

                                    if (!$this->verifyStatusCode($response, 200)){return false;}
                                    return $response;
               
                    // return $obj["attributes"]["id"];
        
        }
            catch(Exeption $e){
    
                return false;
            } 
        
            return false;
        }


        public function list_allocation(){ //work

            try{
                $nb_node = $this->count_node();
                $tab_alloc=[];
                
                if($nb_node>1){
                    while($nb_node>0){
                        $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes/".$nb_node."/allocations");
                        if ($this->verifyStatusCode($response, 200)){ $tab_alloc[$nb_node] = $response;}
                        $nb_node--;
                    }
                }
                else{
                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes/".$nb_node."/allocations");
                    if ($this->verifyStatusCode($response, 200)){return $response;
                    //$this->verifyStatusCode($response, 201);
                        
                                        

                }
                   
                        // return $obj["attributes"]["id"];
                }        

                        
                       
            
            }
        
                catch(Exeption $e){
        
                    return false;
                } 
            
                return false;
            }

            public function list_nest(){ //work

                try{
                    $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nests");
                                        
                        //$this->verifyStatusCode($response, 201);
                                            dump(json_decode($response->getBody()));
                       
                            // return $obj["attributes"]["id"];


                            if ($this->verifyStatusCode($response, 200)){return false;}
                            return $response;
                
                }
                    catch(Exeption $e){
            
                        return false;
                    } 
                
                    return false;
                }


                public function get_id_external($external_id){ //work

                    try{
                        $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."servers/external/".$external_id);
                                            
                            $this->verifyStatusCode($response, 200);

                            dump(json_decode($response->getBody()));
                                                         
                                foreach($response['data'] as $obj){
                                return$obj["attributes"]["id"];
                              }
                    
                    }   
                        catch(Exeption $e){
                
                            return false;
                        } 
                    
                        return false;
                    }
                    
                    public function list_node(){ //work

                        try{
                            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes");
                                                
                                //$this->verifyStatusCode($response, 201);
                                                    dump(json_decode($response->getBody()));
                               
                                    // return $obj["attributes"]["id"];
                                    if ($this->verifyStatusCode($response, 200)){return false;}
                                    return $response;
                        
                        }
                            catch(Exeption $e){
                    
                                return false;
                            } 
                        
                            return false;
                        }

                        public function egg_detail(){ //work

                            try{
                                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nests/2/eggs/9");
                                                    
                                    //$this->verifyStatusCode($response, 201);
                                                        dump(json_decode($response->getBody()));

                                                        if ($this->verifyStatusCode($response, 200)){return false;}
                                                        return $response;
                                   
                                        // return $obj["attributes"]["id"];
                            
                            }
                                catch(Exeption $e){
                        
                                    return false;
                                } 
                            
                                return false;
                            }

                        public function allocation_remaining(){  //tested

                            if($this->list_allocation()!==false){$alloc_ptero = $this->list_allocation();}
                            else{return false;}
                            $count = 0;

                                            dump($alloc_ptero);
                                foreach($alloc_ptero['data'] as $ap){
                                   
                                    if(!$ap['attributes']['assigned']){
                                        $count = $count + 1;
                                    }
                                }
                             
                                return $count;


                        }
                        public function id_allocation_available(){ //tested
                            try{
                                $alloc_ptero=0;
                            if($this->list_allocation() !== false){$alloc_ptero = $this->list_allocation();}
                           else{throw new Exception('no more id available');}
                            //$count;
                            $id=[];
                            $c=0;

                                foreach($alloc_ptero['data'] as $ap){
                                    $c = $c+1;
                                    if(!$ap["attributes"]['assigned']){
                                       
                                        //dump($c);

                                        return $ap["attributes"]['id'];

                                    }
                                    
                                    
                                }
                                return $id;
                            }
                            catch(Exeption $e){

                            }

                            return false;   


                        }


                        public function get_ip_by_id_allocation(String $external_id){ //to test
                            try{
                            //$count;
                            
                           // $id = $this->id_location_available()
                           $node_id;
                           $id_allocation;
                           $ip;
                           $port;

                           $infoserver = $this->getInfoServer($external_id);

                           if($infoserver !==false ){


                                foreach($infoserver->attributes as $info=>$value){

                                    if($info=="node"){
                                        $node_id = $value;
                                    }
                                    elseif($info == "allocation"){
                                        $id_allocation = $value;
                                    }
                               }
                            

                           }
                           else {return false;}



                            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes/".$node_id."/allocations");
                            if (!$this->verifyStatusCode($response, 200)){
                              return false;
                            }
                            dump(json_decode($response->getBody()));

                                foreach($response['data'] as $ap){
                                    dump($id_allocation);
                                    if($ap["attributes"]['id'] ==$id_allocation){
                                        $ip = $ap["attributes"]['ip'];
                                        $port = $ap["attributes"]['port'];
                                        return compact("ip","port");
                                    }

                                }
                            }
                            catch(Exeption $e){

                            }

                               


                        }

                        public function count_node(){ //work

                            try{
                                $i=0;
                                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes");
                                                    
                                    //$this->verifyStatusCode($response, 201);
                                                        

                                                        if (!$this->verifyStatusCode($response, 200)){return false;}
                                                        
                                                        else{
                                                            foreach($response["data"]as $b){
                                                                $i++;
                                                                
                                                            }
                                                        }
                                                        return $i;

       
                                        // return $obj["attributes"]["id"];
                            
                            }
                                catch(Exeption $e){
                        
                                    return false;
                                } 
                            
                                return false;
                            }

}

