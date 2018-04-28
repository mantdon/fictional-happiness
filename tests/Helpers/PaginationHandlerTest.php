<?php

namespace App\Tests;

use App\Services\PaginationHandler;
use App\Tests\Fixtures\LoadServices;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationHandlerTest extends CustomWebTestCase
{
	/**
	 * @var PaginationHandler
	 */
	private $paginationHandler;
	private $session;
	private $em;
	private $requestStack;
	private $client;
	protected static $fixtures = [LoadServices::class];

	public function __construct(?string $name = null, array $data = [], string $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->em = $this->getContainer()->get('doctrine')->getManager();
		$this->session = $this->getContainer()->get('session');
		$this->setupFakeRequest();
		$this->paginationHandler = new PaginationHandler($this->em, $this->session, $this->requestStack);
	}

	public function testItemLimitSetterAndResultCounts(): void
	{
		// Item count per page is correct when valid value is provided.
		$this->paginationHandler->setQuery('App:Service', 'getAll')
			->setItemLimit(2)
			->paginate();

		$this->assertEquals(2, $this->paginationHandler->getResult()->getCount());
		$this->assertEquals(15, $this->paginationHandler->getResult()->getTotalCount());
		$this->assertEquals(ceil(15 / 2), $this->paginationHandler->getPageCount());

		// Item count per page is reset to default when negative value is provided.
		$this->paginationHandler->setQuery('App:Service', 'getAll')
			->setItemLimit(-2)
			->paginate();

		$this->assertEquals(5, $this->paginationHandler->getResult()->getCount());
		$this->assertEquals(15, $this->paginationHandler->getResult()->getTotalCount());
		$this->assertEquals(ceil(15 / 5), $this->paginationHandler->getPageCount());

		// Item count per page is reset to default when value exceeding maximum number of items is provided.
		$this->paginationHandler->setQuery('App:Service', 'getAll')
			->setItemLimit(200)
			->paginate();

		$this->assertEquals(5, $this->paginationHandler->getResult()->getCount());
		$this->assertEquals(15, $this->paginationHandler->getResult()->getTotalCount());
		$this->assertEquals(ceil(15 / 5), $this->paginationHandler->getPageCount());
	}

	public function testPageSetter(): void
	{
		// Items are paginated correctly when valid page number is provided.
		$this->paginationHandler->setQuery('App:Service', 'getAll')
			->setPage(2)
			->setItemLimit(1)
			->paginate();

		$this->assertEquals('Service1', $this->paginationHandler->getResult()->get(0)->getName());

		// Items are paginated correctly when page number < 1.
		$this->paginationHandler->setPage(0)
			->setItemLimit(3)
			->paginate();

		$this->assertEquals('Service0', $this->paginationHandler->getResult()->get(0)->getName());

		// Items are paginated correctly when page number > total number of items.
		$this->paginationHandler->setPage(99)
			->setItemLimit(3)
			->paginate();

		$this->assertEquals('Service0', $this->paginationHandler->getResult()->get(0)->getName());

	}

	public function testLastUsedPageRestoration(): void
	{
		// Set a last used pagination page.
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->setPage(2)
								->setItemLimit(1)
								->addLastUsedPageUseCase('pagination/page')
								->paginate();

		// Last used page is restored with a single use case provided.
		$referer = '/restore/last/pagination/page';
		$this->setupFakeRequest('/', $referer);

		$this->paginationHandler = new PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->setPage(4)
								->setItemLimit(1)
								->addLastUsedPageUseCase('pagination/page')
								->paginate();

		$this->assertEquals(15, $this->paginationHandler->getPageCount());
		$this->assertEquals('Service1', $this->paginationHandler->getResult()->get(0)->getName());

		// Last used page is restored with multiple use cases provided.
		$referer = '/something/and/another/one/somethingafter';
		$this->setupFakeRequest('/', $referer);

		$this->paginationHandler = new PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
			->setPage(6)
			->setItemLimit(1)
			->addLastUsedPageUseCase('pagination/page')
			->addLastUsedPageUseCase('and/another/one')
			->paginate();

		$this->assertEquals(15, $this->paginationHandler->getPageCount());
		$this->assertEquals('Service1', $this->paginationHandler->getResult()->get(0)->getName());
	}

	public function testFinalPageUseCase(): void
	{
		// Final page is loaded with a single use case provided.
		$referer = '/jump/to/final/pagination/page';
		$this->setupFakeRequest('/', $referer);
		$this->paginationHandler = new  PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->setPage(2)
								->setItemLimit(1)
								->addFinalPageUseCase('final/pagination')
								->paginate();

		$this->assertEquals(15, $this->paginationHandler->getPageCount());
		$this->assertEquals('Service14', $this->paginationHandler->getResult()->get(0)->getName());

		// Final page is loaded with multiple use cases provided.
		$referer = '/once/more';
		$this->setupFakeRequest('/', $referer);
		$this->paginationHandler = new  PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->setPage(2)
								->setItemLimit(1)
								->addFinalPageUseCase('/once')
								->addFinalPageUseCase('final/pagination')
								->paginate();

		$this->assertEquals(15, $this->paginationHandler->getPageCount());
		$this->assertEquals('Service14', $this->paginationHandler->getResult()->get(0)->getName());
	}

	public function testCurrentPageOverrideEdgeCase(): void
	{
		$referer = '/very/bad/page';
		$this->setupFakeRequest('/', $referer);
		$this->paginationHandler = new  PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->addFinalPageUseCase('bad/page');

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('final');

		// Exception thrown when adding last used page use case which is already specified as a final page use case.
		$this->paginationHandler->addLastUsedPageUseCase('bad/page');

		$this->paginationHandler = new  PaginationHandler($this->em, $this->session, $this->requestStack);
		$this->paginationHandler->setQuery('App:Service', 'getAll')
								->addLastUsedPageUseCase('bad/page');

		$this->expectExceptionMessage('last used');

		// Exception thrown when adding final page use case which is already specified as a last used page use case.
		$this->paginationHandler->addFinalPageUseCase('bad/page');
	}

	private function setupFakeRequest($url = '/', $referer = '/'): void
	{
		$this->client = static::createClient(array('environment' => 'test'));
		$this->client->request('GET', $url, array(), array(), array('HTTP_REFERER' => $referer));
		$this->requestStack = new RequestStack();
		$this->requestStack->push($this->client->getRequest());
	}
}