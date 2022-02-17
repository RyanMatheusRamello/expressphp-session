<?php
namespace ExpressPHP\Plugins {
    class Session {
        private $initSess;
        public function __construct(bool $initSess){
            $this->initSess = $initSess;
        }
        public function set($name, $value){
            if(!$this->initSess) session_start();
            $d = explode(".", $name);
            $result = [];
            foreach($d as $val){
                if(count($result) == 0){
                    $result[] = &$_SESSION;
                }
                if(!isset($result[count($result)-1][$val])){
                    $result[count($result)-1][$val] = null;
                }
                $result[] = &$result[count($result)-1][$val];
            }
            $result[count($result)-1] = $value;
            return true;
        }
        public function get($name=null){
            if(is_null($name)){
                return $_SESSION ?? [];
            }
            $d = explode(".", $name);
            $result = null;
            foreach($d as $val){
                if(is_null($result)){
                    $result = $_SESSION ?? [];
                }
                if(!isset($result[$val])){
                    return null;
                }
                $result = $result[$val];
            }
            return $result;
        }
        public function destroy(){
            if(!$this->initSess) return;
            session_destroy();
        }
        public function unset($name){
            if(!$this->initSess) return;
            $d = explode(".", $name);
            $result = [];
            foreach($d as $val){
                if(count($result) == 0){
                    $result[] = &$_SESSION;
                }
                if(!isset($result[count($result)-1][$val])){
                    $result[count($result)-1][$val] = null;
                }
                $result[] = &$result[count($result)-1][$val];
            }
            $result[count($result)-1] = null;
            return true;
        }
        public function reset(){
            if(!$this->initSess) return false;
            session_reset();
            return true;
        }
        public function __debugInfo(){
            return $_SESSION ?? [];
        }
        static public function run(array $options = []){
            return function($req, $res, $next) use ($options) {
                $name = $options["name"] ?? "expressphp_session";
                if(!isset($options["secret"])){
                    throw new \Error("Você deve informar um secret");
                }
                session_name($name);
                if(!isset($options["cookie"])){
                    $options["cookie"] = ["path" => "/"];
                }
                if(!isset($options["cookie"]["path"])){
                    $options["cookie"]["path"] = "/";
                }
                session_set_cookie_params($options["cookie"]);
                if(!isset($options["genid"])){
                    $options["genid"] = function($req){
                        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $randstring = '';
                        for ($i = 0; $i < 32; $i++) {
                            $r = strlen($characters);
                            $randstring .= $characters[rand(0, $r-1)];
                        }
                        return $randstring;
                    };
                }
                if(isset($req->cookies[$name])){
                    $req->session = new Session(true);
                }else{
                    $id = $options["genid"]($req);
                    session_id(hash_hmac("sha256", $id, $options["secret"]));
                    $req->session = new Session(false);
                }
                $next();
            };
        }
    }
}
