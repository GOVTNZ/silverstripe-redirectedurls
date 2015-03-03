<?php

/**
 * RedirectedURLExpiryTask looks for expired redirect rules and deletes them.
 */
class RedirectedURLExpiryTask extends BuildTask {
	public $description = 'Remove expired redirector URL rules';

	public function init() {
		parent::init();
		
		if(!Permission::check('ADMIN')) {
			return Security::permissionFailure($this);
		}
	}

	public function run($request) {

		// @todo if RedirectedURL is extended to support other expiry semantics, this should not filter in SQL.
		$expired = DataObject::get("RedirectedURL", "\"ExpiryDate\" is not null and \"ExpiryDate\" <= CURDATE()");
		foreach ($expired as $e) {
			$e->delete();
		}
	}
}