<?

namespace Mtero_request\Manage_Ptero;
use Illuminate\Http\Request;
use App\http\Models;
use Illuminate\Support\Facades\Http;

class servers
{

    protected $user;//Instance de user
    protected $uri ;
    protected $endpoint;
    protected $apikey;
    


    public function __construct(User $user, String $uri, $apikey)
    {
        $this->user = $user;
        $this->external_id = $external_id;
        $this->uri = $uri;
        $this->apikey = $apikey;
        $this->endpoint = "api/application  /";
    }

    private function verifyStatusCode(String $response, int $code_valid){

        if($response->getStatusCode() !== $code_valid){
            throw new Exception('Invalid Response');
        }
    }

    //debut server 
    public function getUserIdPtero(){

        $email =$this->user->email;

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint."/users",[
                "email"=>(String)$email    
            ]);
       
                $this->verifyStatusCode($response, 201);

                foreach($response['data'] as $obj){
                    return $obj["attributes"]["id"];
            }
        }
            catch(Exeption $e){
    
                return false;
            } 
        
            return false;
        }
        public function getIdServers($external_id){

            $email =$this->user->email;
    
            try{
                $response = Http::timeout(400)->withToken($this->apikey)->post($this->uri.$this->endpoint."/server/external/".$external_id);
                                    
                    $this->verifyStatusCode($response, 201);
    
                    foreach($response['data'] as $obj){
                        return $obj["attributes"]["id"];
                }
            }
                catch(Exeption $e){
        
                    return false;
                } 
            
                return false;
            }
    


    public function createServer(Array $option=[]){

        try{
            if(isset($option["external_id"])&&isset($option["name"])&&isset($option["egg"])&&isset($option["docker_img"])&&isset($option["startup"])&&isset($option["environment"])&&isset($option["limits"])&&isset($option["features_limits"])&&isset($option["allocations"])){
            $encode = [
                "external_id"=>(String)$option["external_id"],
                "name"=>(String)$option["name"],
                "user"=>$this->getUserIdPtero(),
                "egg"=>(int)$option['egg'],
                "docker_image"=>(String)$option["docker_img"],
                // "auto_deploy"=>true,
                "limits"=>$option['limits'],
                "environment"=>(String)$option["environment"],
                "feature_limits"=>$option["features_limits"],
                "allocation"=>(string)$option['allocations']
               ];}
               else throw new Exeption('left one or a lot lel param');
            $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint."/servers",$encode);
       
                $this->verifyStatusCode($response, 201);
        }
            catch(Exeption $e){
    
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

        $response = Http::timeout(400)->withToken($this->apikey)->post($this->uri.$this->endpoint."/server/".$this->getIdServers($external_id).'/'.$method);

            $this->verifyStatusCode($response, 204);

        }
        catch(Exeption $e){

            return false;
        } 
        return true;     
    }
    
    public function deleteServer(String $external_id ,bool $force){
        if(isset($external_id) && isset($force))
        $force = "force";
        $force = ($force) ?: '';
        try{
            $response = Http::timeout(400)->withToken($this->apikey)->delete($this->uri.$this->endpoint."/server/".$this->getIdServers($external_id).'/'.$force);    
                $this->verifyStatusCode($response, 204);
            }
            catch(Exeption $e){
    
                return false;
            } 
        return true;     

    }
    public function ModifServer(String $external_id, Array $option=[] ,bool $build=false, $details=false, bool $startup=false ){

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
                
        
                if($build){$verif=1;$method=$startup_verif;$req=["allocation"=> (int)$option["allocation"], (int)"memory"=>$option['limits']['memory'], (int)"swap"=> $option['limits']['swap'], "disk"=> (int)$option['limits']['disk'],"io"=> (int)$option['limits']["io"],"cpu"=>(int)$option['limits']["cpu"],"threads"=> null,"feature_limits"=> ["databases"=> (int)$option['features_limits']['databases'],(int)"allocations"=> $option['features_limits']['allocation'],"backups"=> (int)$option['backups']]];}
                elseif($details  && !$verif){$verif=1;$method=$details_verif;$req=["name"=> (String)$option['name'], "user"=>$this->getUserIdPtero(), "external_id"=>(String)$option['external_id'], "description"=> (String)$option['description']];}
                elseif($startup && !$verif){$method=$build_verif;$verif=1;$req=["startup"=>(String) $option['startup'],"environment"=>(String)$option['environment'], "egg"=>(int)$option['egg'], "image"=>(String)$option['image'],"skip_scripts"=>false];}

            $response = Http::timeout(400)->withToken($this->apikey)->patch($this->uri.$this->endpoint."/server/".$this->getIdServers($external_id).$method, $req);

               if($response->getBody() == null){throw new Exeption('invalid response');}
            }
            catch(Exeption $e){
    
                return false;
            } 
        return true;     

    }

    // public function CreateUser()
    // {
    //     $current_email = $this->user->email;

    // }



}

