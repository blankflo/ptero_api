<?php
namespace Mtero_request\Manage_Ptero;

use Illuminate\Http\Request;
use App\http\Models;
use Illuminate\Support\Facades\Http;

class client
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
        $this->endpoint = "api/application/user/";
    }

    private function verifyStatusCode(String $response, int $code_valid){

        if($response->getStatusCode() !== $code_valid){
            throw new Exception('Invalid Response');
        }
    }



    private function GetUserId(){
        $id_user = $this->user->id;
        $return_array= [];

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint.$id_user);
    
                $this->verifyStatusCode($response, 200);
            }
        catch(Exeption $e){
    
                return false;
            }
            foreach($parsed['data'] as $obj){
                return $obj["attributes"]["id"];
                
            }
            return false;     

    }

    public function updateInfoUser(String $language="fr")
    {
        try{
        
        $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint.$this->getUserId(),[

            "email"=>$this->user->email,
            "username"=> $this->user->username,
            
            "first_name"=>$this->user->first_name,
            "last_name"=> $this->user->last_name,
            
            "language"=>$language,
            
            "password"=>$this->user->$password
            

            ]);

            $this->verifyStatusCode($response, 201);
        }
        catch(Exeption $e){

            return false;
        }
        return false;     
    }
    
    public function CreateUser(){

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint."/user" ,[
    
                "email"=>$this->email,
                "username"=>$this->username,
                "first_name"=>$this->first_name,
                "last_name"=>$this->last_name
                
                
                ]);
    
                $this->verifyStatusCode($response, 201);
            }
            catch(Exeption $e){
    
                return false;
            } 
        return true;     

    }
    public function delete_user(){

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->delete($this->uri.$this->endpoint."/user"/$this->GetUserId());
    
                $this->verifyStatusCode($response, 204);
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
