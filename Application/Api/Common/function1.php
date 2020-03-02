<?php
function get_url_headers($url,$timeout=10){
		$ch=curl_init();
	
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_NOBODY,true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
	
		$data=curl_exec($ch);
		$data=preg_split('/\n/',$data);
	
		$data=array_filter(array_map(function($data){
			$data=trim($data);
			if($data){
				$data=preg_split('/:\s/',trim($data),2);
				$length=count($data);
				switch($length){
					case 2:
					return array($data[0]=>$data[1]);
					break;
					case 1:
					return $data;
					break;
					default:
						break;
				}
			}
		},$data));
	
			sort($data);
			foreach($data as $key=>$value){
				$itemKey=array_keys($value)[0];
				if(is_int($itemKey)){
					if(stristr($value[$itemKey],"HTTP/")!=false){
						$data[0]=$value[$itemKey];
					}else{
						$data[$key]=$value[$itemKey];
					}
					
				}elseif(is_string($itemKey)){
					$data[$itemKey]=$value[$itemKey];
					unset($data[$key]);
				}
			}
	
			return $data;
	}