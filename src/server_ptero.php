<?php
namespace App\Classes;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class Server_ptero
{
    protected $user;//Instance de user
    protected $uri;
    protected $endpoint;
    protected $apikey;
    


    public function __construct(User $user, String $uri, $apikey)
    {
        $this->user = $user;
        $this->uri = $uri;
        $this->apikey = $apikey;
        $this->endpoint = "api/application/";
    }

    private function verifyStatusCode($response, int $code_valid){

        try{
        if($response->getStatusCode() !== $code_valid || $response->getBody()==null){
           
            return false;        }
         else {return true; }    
           

    }catch(Exeption $e){ return false ;}
        
    }

    public function getUserIdPtero(){
        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."users");
         
                
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

    
    public function getInfoServer(int $external_id){

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."servers/".$external_id);
                                
                //$this->verifyStatusCode($response, 201);

                if ($this->verifyStatusCode($response, 201)){return false;}

            $response = $response->getBody();
                
            return $response;
        
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
    


    public function createServer(){

    try{
            // if(isset($option["external_id"])&&isset($option["name"])&&isset($option["egg"])&&isset($option["docker_img"])&&isset($option["startup"])&&isset($option["environment"])&&isset($option["limits"])&&isset($option["features_limits"])&&isset($option["allocations"])){
            $encode = [
                // "external_id"=>"michou",
                "name"=>"sdjflkfjlhleiuhf",
                "user"=>1,
                "egg"=>9,
                "description"=>"mdsmlfdls",
                "docker_img"=>"quay.io/pterodactyl/core:source",
                "startup" =>"./srcds_run -game garrysmod -console -port {{SERVER_PORT}} +ip 0.0.0.0 +host_workshop_collection {{WORKSHOP_ID}} +map {{SRCDS_MAP}} +gamemode {{GAMEMODE}}",
                // "auto_deploy"=>true,
                "limits"=>[                
                "memory"=>64, 
                "swap"=>0, 
                "disk"=>1000, 
                "io"=>500, 
                "cpu"=>25
                 ],
                "environment"=>[
                    "SRCDS_MAP" => "gm_flatgrass",
                     "SRCDS_APPID"=>"4020",             
                    "GAMEMODE" => "sandbox",
                    "TICKRATE"=>22,
                    "MAX_PLAYERS"=>32
                  ],
                "feature_limits"=>["databases"=> 5,
                "backups"=> 1],
                "allocations"=>["default"=>2]];
                  
             //    ];}}
        
            // dump($this->listEgg());

        
              
            $response = Http::timeout(30)->withToken($this->apikey)->post("https://app.nexifi.games/api/application/servers",$encode);

            if ($this->verifyStatusCode($response, 201)){return false;}
            
            dump(json_decode($response->getBody()));

            dump($response);
                  
                }
                catch(Exeption $e)
                {
                    return false;
                }

            return true;

    }

    
    public function actionServerPtero(String $external_id, String $method)
    {
        try{
            if($method !== "suspend" || $method !== "unuspend "|| $method !== "reinstall"){
                throw new Exeption("bad method use");
            }

        $response = Http::timeout(400)->withToken($this->apikey)->post($this->uri.$this->endpoint."server/".$this->getIdServers($external_id).'/'.$method);

        if ($this->verifyStatusCode($response, 204)){return false;}

        }
        catch(Exeption $e){

            return false;
        } 
        return true;     
    }
    
    public function deleteServer(String $id_server ,bool $force){
        if(isset($external_id) && isset($force)){
        $force = "force";
        $force = ($force) ?: '';
        
        try{
            $response = Http::timeout(400)->withToken($this->apikey)->DELETE($this->uri.$this->endpoint."servers/".$id_sever);    
            dump(json_decode($response->getBody()));
                $this->verifyStatusCode($response, 204);
            }
            catch(Exeption $e){
    
                return false;
            } }
        else{return false;}
        return true;     

    }
    public function ModifServer(bool $build=false, $details=false, bool $startup=false ,String $external_id, Array $option  ){

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

            $response = Http::timeout(400)->withToken($this->apikey)->patch($this->uri.$this->endpoint."server/".$this->getIdServers($external_id).$method, $req);

               if($response->getBody() == null){throw new Exeption('invalid response');}
            }
            catch(Exeption $e){
    
                return false;
            } 
        return true;     

    }

    
    public function listEgg(){

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


            
    public function list_server(){

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri."api/application/servers");
                                
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


        public function list_allocation(){

            try{
                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nodes/1/allocations");
                                    
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

            public function list_nest(){

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


                public function get_id_external($external_id){

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
                    
                    public function list_node(){

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

                        public function egg_detail(){

                            try{
                                $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint."nests/2/eggs/9");
                                                    
                                    //$this->verifyStatusCode($response, 201);
                                                        dump(json_decode($response->getBody()));
                                   
                                        // return $obj["attributes"]["id"];
                            
                            }
                                catch(Exeption $e){
                        
                                    return false;
                                } 
                            
                                return false;
                            }

}

