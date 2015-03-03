<?php

/**
 * RedirectedURLPageExtension adds a tab for redirection rules to pages in the CMS. This allows
 * CMS users to see and manage redirect rules that target that specific page, as an alternative
 * to the model admin.
 */
class RedirectedURLPageExtension extends DataExtension {

	public function updateCMSFields(FieldList $fields) {
		$gridConfig = GridFieldConfig::create()->addComponents(
			new GridFieldToolbarHeader(),
			new GridFieldAddNewButton('toolbar-header-right'),
			new GridFieldSortableHeader(),
			new GridFieldDataColumns(),
			new GridFieldPaginator(20),
			new GridFieldEditButton(),
			new GridFieldDeleteAction(),
			new GridFieldDetailForm()
		);
		$redirectsField = new GridField(
			"Redirects",
			"Redirects to this page",
			null,
			$gridConfig
		);
		$redirectsField->setModelClass('RedirectedURL');
		$redirectsField->setList($this->getRedirectionsToThis());
		$fields->addFieldToTab('Root.Redirects', $redirectsField);
	}

	// Return a data set of RedirectedURL objects where the target of the redirect
	// is this page.
	// Implementation note: fetches all redirect rules, and builds a new, filtered
	// set in memory. While filtering would be faster in SQL, (a) this is only invoked
	// in the CMS and (b) we can be more flexible with filtering in PHP than in SQL
	// unless we're prepared for painful query. In particular, filtering in PHP means
	// that we can excluded cases from the To URL such as query fields, fragments and
	// absolute URLs, and we can also be tolerant towards admins entering in relative
	// URLs (e.g. "organisations", which should match "/organisations/" URL.)
	protected function getRedirectionsToThis() {
		$items = RedirectedURL::get();
		$result = new ArrayList();
		foreach ($items as $r) {
			if ($this->isRedirectToSelf($r->To)) {
				$result->push($r);
			}
		}
		return $result;
	}

	// Given a value $r, which is generally a relative URL on the site, optionally with query fields
	// and/or fragments, determine if it is a link to this page. The match is tolerant to missing
	// leading/trailing slashes as returned by the Link function.
	public function isRedirectToSelf($r) {
		$thisLink = $this->owner->Link();

		// ignore full links
		if (strpos($r, '://') !== FALSE) {
			return false;
		}

		// truncate query fields off
		$qi = strpos($r, '?');
		if ($qi !== FALSE) {
			$r = substr($r, 0, $qi);
		}

		// truncate fragments off
		$fi = strpos($r, '#');
		if ($fi !== FALSE) {
			$r = substr($r, 0, $fi);
		}

		// normalise so that leading and trailing spaces are the same as per Link(), i.e. there is both
		// a leading and trailing slash. If it's home page, it's a single slash.
		if ($r == "") {
			$r = "/";
		}
		if (substr($r, 0, 1) != "/") {
			$r = "/" . $r;
		}
		if (substr($r, -1) != "/") {
			$r .= "/";
		}

		return $r == $thisLink;
	}
}