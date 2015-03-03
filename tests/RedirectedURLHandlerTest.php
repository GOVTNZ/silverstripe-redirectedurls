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

		Debug::show($page->Link());
		Debug::show("base:" . Director::baseURL());

		$this->assertFalse($page->isRedirectToSelf("other-url"), "relative URL to somewhere else");
		$this->assertFalse($page->isRedirectToSelf("http://test-url"), "absolute URL");
		$this->assertTrue($page->isRedirectToSelf("test-url"), "no slash");
		$this->assertTrue($page->isRedirectToSelf("/test-url"), "front slash");
		$this->assertTrue($page->isRedirectToSelf("test-url/"), "trail slash");
		$this->assertTrue($page->isRedirectToSelf("/test-url/"), "both slash");
		$this->assertTrue($page->isRedirectToSelf("/test-url?foo=bar"), "with query");
		$this->assertTrue($page->isRedirectToSelf("test-url?foo=bar#frag"), "with query and frag");
		$this->assertTrue($page->isRedirectToSelf("test-url#frag"), "with frag");
	}
}

class RedirectedURL_TestPage extends Page implements TestOnly {
	private static $extensions = array(
		'RedirectedURLPageExtension'
	);
}