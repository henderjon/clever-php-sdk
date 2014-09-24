<?php

namespace Clever;

class CleverApi extends CleverApiRequest {

	function __construct($token){
		$this->setToken($token);
	}

	protected $objectMap = [
		"district" => "\\Clever\\CleverDistrict",
		"school"   => "\\Clever\\CleverSchool",
		"section"  => "\\Clever\\CleverSection",
		"teacher"  => "\\Clever\\CleverTeacher",
		"student"  => "\\Clever\\CleverStudent",
		"event"    => "\\Clever\\CleverEvent",
		"status"   => "\\Clever\\CleverStatus",
	];

	function newObject($type, $id, array $data = []){
		if(class_exists($type)){
			$object = new $type($this->token, $id, $this->logger);
			if($data){
				$object = $this->unmarshal($object, $data);
			}
		}
		return $object;
	}

	function __call($name, $args){
		if(isset($this->objectMap[$name])){
			$object = $this->objectMap[$name];
			$Obj = new $object($args[0], function($url, array $query = []){
				list($body, $code) = $this->ping($url, $query);
				if($code != 200){
					$this->relayApiError(new Exceptions\CleverApiException($body, $code));
				}
				return json_decode($body, true);
			});
			return $Obj->retrieve();
		}
	}

	/**
	 *
	 */
	function relayApiError(Exceptions\CleverApiException $e){

		switch ($e->getCode()) {
		case 400:
		case 404:
			$e = new Exceptions\InvalidRequestException($e->getMessage(), $e->getCode(), $e);
			break;
		case 401:
			$e = new Exceptions\AuthenticationException($e->getMessage(), $e->getCode(), $e);
			break;
		case 402:
			$e = new Exceptions\InvalidRequestException($e->getMessage(), $e->getCode(), $e);
			break;
		default:
			$e = new Exceptions\CleverError($e->getMessage(), $e->getCode(), $e);
			break;
		}

		if($this->logger instanceof Log\LoggerInterface){
			$this->logger->error($e->getMessage(), [
				"APIBASE"    => static::APIBASE,
				"VERSION"    => static::VERSION,
			]);
		}

		throw $e;
	}

}