<?php

namespace Clever;

class CleverSection extends CleverObject {

	const BASE_URL = "sections";

	function getDistrict(array $query = []){
		return $this->getObjects(static::CLEVER_DISTRICT, $query);
	}

	function getSchool(array $query = []){
		return $this->getObjects(static::CLEVER_SCHOOL, $query);
	}

	function getStudents(array $query = []){
		return $this->getObjects(static::CLEVER_STUDENT, $query);
	}

	function getTeacher(array $query = []){
		return $this->getObjects(static::CLEVER_TEACHER, $query);
	}

	function getEvents(array $query = []){
		return $this->getObjects(static::CLEVER_EVENT, $query);
	}

	function getStatus(array $query = []){
		return $this->getObjects(static::CLEVER_STATUS, $query);
	}

}