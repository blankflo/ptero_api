<?php

namespace App\Classes;
 
use Illuminate\Support\Facades\Http;
use App\Models\User;
 use Exception;

class Application
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
         
                
            dump(json_decode($response->getBody()));

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

    // public function GetUserId(){
    //     $id_user = $this->user->id;
    //     $return_array= [];

    //     try{
    //         $response = Http::timeout(400)->withToken($this->apikey)->get($this->uri.$this->endpoint.$id_user);
    
    //            // $this->verifyStatusCode($response, 200);
    //         }
    //     catch(Exeption $e){
    
    //             return false;
    //         }
    //         foreach($response['data'] as $obj){
    //             return $obj["attributes"]["id"];
                
    //         }
    //         return false;     

    // }

    public function updateInfoUser(String $language="en")
    {

        $request = [ "email"=>$this->user->email,
                     "username"=> $this->user->username,

                    "first_name"=>$this->user->name,
                    "last_name"=> $this->user->lastname,
        
                    "language"=>$language,
        
                    "password"=>$this->user->password];
        try{
        
        $response = Http::timeout(400)->withToken($this->apikey)->patch($this->uri.$this->endpoint."users/".$this->getUserIdPtero(),$request);

            foreach($response["attributes"] as $obj=>$value){   
                switch($obj){
                    case "username":
                        if($request["username"] !== $value){return false;}
                    break;

                    case "email":
                        if($request["email"] !== $value){return false;}
                    break;

                    case "first_name":
                        if($request["first_name"] !== $value){return false;}
                    break;

                    case "last_name":
                        if($request["last_name"] !== $value){return false;}
                    break;

                    case "language":
                        if($request["language"] !== $value){return false;}
                    break;
                    default:
                       break;
                }
                
            }

            if(!$this->verifyStatusCode($response, 200)){return false;}
        }
        catch(Exeption $e){

            return false;
        }
        return true;     
    }

    // public function Getinfosserveur(){

    //     $email =$this->user->email;

    //     try{
    //         $response = Http::timeout(400)->withToken($this->apikey)->put($this->uri.$this->endpoint."/users",[
    //             "email"=>(String)$email    
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
    
    public function CreateUser(){
 
            dump($this->user->email);
            dump($this->user->name);
            dump($this->user->username);
    
        try{
            $response = Http::withToken($this->apikey)->post($this->uri.$this->endpoint."users",[
    
                "email"=>$this->user->email,
                "username"=>$this->user->first_name." ".$this->user->last_name,
                "first_name"=>$this->user->name,
                "last_name"=>"michou"
                
                ]);
                dump(json_decode($response->getBody()));

             //  if( $this->verifyStatusCode($response, 201) == false){return false;};
             if($this->getUserIdPtero()!==false){return true;}
            }
            catch(Exeption $e){
    
                return false;
           
        
            }
    return false;

    }
    public function delete_user(){

        try{
            $response = Http::timeout(400)->withToken($this->apikey)->delete($this->uri.$this->endpoint."user/".$this->getUserIdPtero());
    
          //  if(!$this->verifyStatusCode($response, 204)){return false;}
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
