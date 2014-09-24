<?php

namespace Clever;

class CleverObject {

	const CLEVER_DISTRICT = "district";
	const CLEVER_SCHOOL   = "schools";
	const CLEVER_TEACHER  = "teachers";
	const CLEVER_STUDENT  = "students";
	const CLEVER_SECTION  = "sections";
	const CLEVER_EVENT    = "events";
	const CLEVER_STATUS   = "status";

	protected $data = [];

	protected $id;

	protected $pinger;

	function __construct($id, callable $pinger){
		$this->setId($id);
		$this->pinger = $pinger;
	}

	protected function makeUrl($endpoint){
		$url = static::BASE_URL . "/{$this->id}";
		if($endpoint){
			$url = "{$url}/{$endpoint}";
		}
		return $url;
	}

	function retrieve(){
		$data = call_user_func($this->pinger, $this->makeUrl(""), []);
		return $this->unmarshal($data);
	}

	function unmarshal(array $data = []){
		foreach($data as $key => $value){
			$this->data[$key] = $value;
		}
		return $this;
	}

	function __set($property, $value){
		$this->data[$property] = $value;
	}

	function __get($property){
		if(!isset($this->data[$property])){
			return null;
		}
		return $this->data[$property];
	}

	function setId($id){
		$this->id = $this->data["id"] = $id;
	}

	function serialize(){
		return json_encode([
			"type" => get_class($this),
			"data" => $this->data,
		]);
	}

	protected function getObjects($type, array $query = []){
		if( $typedObject = $this->getTypedObject($type) ){
			$data = call_user_func($this->pinger, $this->makeUrl($type), []);
			foreach($data["data"] as $object){
				$Obj = new $typedObject($object["data"]["id"], $this->pinger);
				$Objs[] = $Obj->unmarshal($object["data"]);
			}
			return count($Objs) == 1 ? reset($Objs) : $Objs;
		}
	}

	protected function getTypedObject($type){
		$objectMap = [
			static::CLEVER_DISTRICT => __NAMESPACE__ . "\\CleverDistrict",
			static::CLEVER_SCHOOL   => __NAMESPACE__ . "\\CleverSchool",
			static::CLEVER_SECTION  => __NAMESPACE__ . "\\CleverSection",
			static::CLEVER_TEACHER  => __NAMESPACE__ . "\\CleverTeacher",
			static::CLEVER_STUDENT  => __NAMESPACE__ . "\\CleverStudent",
			static::CLEVER_EVENT    => __NAMESPACE__ . "\\CleverEvent",
			static::CLEVER_STATUS   => __NAMESPACE__ . "\\CleverStatus",
		];

		if(isset($objectMap[$type])){
			return $objectMap[$type];
		}

		return null;

	}

}