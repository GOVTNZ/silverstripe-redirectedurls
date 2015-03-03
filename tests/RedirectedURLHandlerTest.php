<?php

/**
 * @package redirectedurls
 * @subpackage tests
 */
class RedirectedURLHandlerTest extends FunctionalTest {
	
	static $fixture_file = 'redirectedurls/tests/RedirectedURLHandlerTest.yml';
	
	function setUp() {
		parent::setUp();
		
		$this->autoFollowRedirection = false;
	}
	
	function testHandleURLRedirectionFromBase() {
		$redirect = $this->objFromFixture('RedirectedURL', 'redirect-signups');
		
		$response = $this->get($redirect->FromBase);
		$this->assertEquals(301, $response->getStatusCode());
		
		$this->assertEquals(
			Director::absoluteURL($redirect->To),
			$response->getHeader('Location')
		);
	}
	
	function testHandleURLRedirectionWithQueryString() {
		$response = $this->get('query-test-with-query-string?foo=bar');
		$expected = $this->objFromFixture('RedirectedURL', 'redirect-with-query');
		
		$this->assertEquals(301, $response->getStatusCode());
		$this->assertEquals(
			Director::absoluteURL($expected->To),
			$response->getHeader('Location')
		);
	}
	
	function testArrayToLowercase() {
		$array = array('Foo' => 'bar', 'baz' => 'QUX');
		
		$cont = new RedirectedURLHandler();

		$arrayToLowercaseMethod = new ReflectionMethod('RedirectedURLHandler', 'arrayToLowercase');
		$arrayToLowercaseMethod->setAccessible(true);
		
		$this->assertEquals(
			array('foo'=> 'bar', 'baz' => 'qux'),
			$arrayToLowercaseMethod->invoke($cont, $array)
		);
	}

	function testRuleToPageMatching() {
		$page = new RedirectedURL_TestPage();
		$page->URLSegment = "test-url";

		$this->assertEquals($page->isRedirectToSelf("other-url"), false);
		$this->assertEquals($page->isRedirectToSelf("http://test-url"), false);
		$this->assertEquals($page->isRedirectToSelf("test-url"), true);
		$this->assertEquals($page->isRedirectToSelf("/test-url"), true);
		$this->assertEquals($page->isRedirectToSelf("test-url/"), true);
		$this->assertEquals($page->isRedirectToSelf("/test-url/"), true);
		$this->assertEquals($page->isRedirectToSelf("/test-url?foo=bar"), true);
		$this->assertEquals($page->isRedirectToSelf("test-url?foo=bar#frag"), true);
		$this->assertEquals($page->isRedirectToSelf("test-url#frag"), true);
	}
}

class RedirectedURL_TestPage extends Page implements TestOnly {
	private static $extensions = array(
		'RedirectedURLPageExtension'
	);
}