<?php
/**
 * Created by PhpStorm.
 * User: softia
 * Date: 11/04/18
 * Time: 16:23
 */

class block_mahara_iena_connexion
{
    public $wstoken;
    public $base;
    public $url;

    /**
     * block_mahara_iena_connexion constructor.
     * @param $wstoken
     * @param $url
     */
    public function __construct($wstoken, $base)
    {
        $this->wstoken = $wstoken;
        $this->base = $base;
    }


    public function httpPost($params)
    {
        $ch = curl_init();
        $postdata = htmlspecialchars_decode(urldecode($params)); //Il faut absolument faire ça car il faut enlever les ;amp ect ...
        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $output=curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    function httpGet()
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //  curl_setopt($ch,CURLOPT_HEADER, false);
        $output=curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function create_url($function){
        $this->url = $this->base."?wstoken=".$this->wstoken."&wsfunction=".$function."&alt=json";
    }

    public function getMaharaGroups(){
        $getMaharaGroupes = "mahara_group_get_groups";
        $this->create_url($getMaharaGroupes);
        return json_decode($this->httpGet());
    }

    public function getMaharaUsers(){
        $getMaharaUsers = "mahara_user_get_users";
        $this->create_url($getMaharaUsers);
        return json_decode($this->httpGet());
    }

}