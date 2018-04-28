<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaginationHandler
{
	/** @var EntityManagerInterface */
	private $entityManager;
	/** @var SessionInterface */
	private $session;
	/** @var RequestStack */
	private $requestStack;
	/** @var Paginator */
	private $paginator;
	private $page;
	private $itemLimit;
	private const defaultItemLimit = 5;
	/** @var PaginationResult */
	private $paginationResult;

	private $trackLastUsedPage = false;
	private $trackFinalPage = false;
	private $lastUsedPage;
	private $lastUsedPageUseCases;
	private $finalPageUseCases;

	/**
	 * PaginationHandler constructor.
	 * @param EntityManagerInterface $entityManager
	 * @param SessionInterface       $session
	 * @param RequestStack           $requestStack
	 */
	public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, RequestStack $requestStack)
	{
		$this->entityManager = $entityManager;
		$this->itemLimit = self::defaultItemLimit;
		$this->session = $session;
		$this->requestStack = $requestStack;
		$this->finalPageUseCases = [];
		$this->lastUsedPageUseCases = [];
		$this->loadLastUsedPage();
	}

	/**
	 * Sets the query for pagination using entity repository method.
	 * @param string $className Class name of entity, the repository of which to use.
	 * @param string $method Method name to call from the repository.
	 * @param array  $args Arguments for the repository's method.
	 * @return PaginationHandler
	 */
	public function setQuery(string $className, string $method, ...$args): self
	{
		$repository = $this->entityManager->getRepository($className);
		$query = \call_user_func_array(array($repository, $method), $args);
		$this->paginator = new Paginator($query);
		return $this;
	}

	/**
	 * Sets the current pagination page.
	 * @param int $page Number to set the current pagination page to.
	 * @return PaginationHandler
	 */
	public function setPage(int $page): self
	{
		$this->page = $page;
		return $this;
	}

	/**
	 * Sets the number of items to display per page.
	 * @param int $itemLimit Number of items to display per page.
	 * @return PaginationHandler
	 */
	public function setItemLimit(int $itemLimit): self
	{
		$this->itemLimit = $itemLimit;
		return $this;
	}

	/**
	 * Enables the handler to keep track of last used pagination page.
	 * When referred to the url of the paginated list from a url, containing
	 * the specified partial path, items of the last used pagination page will
	 * be loaded, instead of those of any currently specified page.
	 * @param string $partialPath path to look for in referring url, before loading
	 * last used pagination page.
	 * @return PaginationHandler
	 * @throws \InvalidArgumentException when trying to specify a partial path
	 * to load last used page when referred from an url containing said path, whilst
	 * matching the same partial would result in loading the final page of
	 * pagination.
	 */
	public function addLastUsedPageUseCase(string $partialPath): self
	{
		if(\in_array($partialPath, $this->finalPageUseCases, true))
		{
			throw new \InvalidArgumentException(sprintf('%s is already specified as final page use case.', $partialPath));
		}
		$this->trackLastUsedPage = true;
		if(!\in_array($partialPath, $this->lastUsedPageUseCases, true)){
			$this->lastUsedPageUseCases[] = $partialPath;
		}
		return $this;
	}

	/**
	 * When referred to the url of the paginated list from a url, containing the
	 * specified partial path, items of the final page of pagination will be loaded,
	 * instead of those of any currently specified page.
	 * @param string $partialPath path to look for in referring url, before loading
	 * the final pagination page.
	 * @return PaginationHandler
	 * @throws \InvalidArgumentException when trying to specify a partial path
	 * to load final page when referred from an url containing said path, whilst
	 * matching the same partial would result in loading the last used page of
	 * pagination.
	 */
	public function addFinalPageUseCase(string $partialPath): self
	{
		if(\in_array($partialPath, $this->lastUsedPageUseCases, true))
		{
			throw new \InvalidArgumentException(sprintf('%s is already specified as last used page use case.', $partialPath));
		}
		$this->trackFinalPage = true;
		if(!\in_array($partialPath, $this->finalPageUseCases, true)){
			$this->finalPageUseCases[] = $partialPath;
		}
		return $this;
	}

	/**
	 * Executes the provided query using all provided parameters and allows
	 * the retrieval of results.
	 * @throws \BadMethodCallException if called before a query has been provided.
	 */
	public function paginate(): void
	{
		if($this->paginator === null)
		{
			throw new \BadMethodCallException(sprintf('Cannot paginate before a query has been set. Use setQuery() first.'));
		}

		$this->validateItemLimitAndCurrentPage();

		$firstElementOffset = $this->getFirstElementOffset();
		$this->paginator->getQuery()
						->setFirstResult($firstElementOffset)
						->setMaxResults($this->itemLimit);

		$this->paginationResult = new PaginationResult($this->paginator);
	}

	/**
	 * @return PaginationResult the iterable result of the pagination query.
	 * @throws \BadMethodCallException if called before pagination query has been executed.
	 */
	public function getResult(): PaginationResult
	{
		if($this->paginationResult === NULL){
			throw new \BadMethodCallException('Cannot return pagination results before pagination was executed. 
												Use paginate() before fetching the result.');
		}
		return $this->paginationResult;
	}

	/**
	 * @return int Total number of pages needed to display all queried items.
	 */
	public function getPageCount(): int
	{
		return ceil($this->paginator->count() / $this->itemLimit);
	}

	/**
	 * @return mixed Number of last accessed pagination page, if it's being tracked,
	 * NULL otherwise.
	 */
	public function getLastUsedPage()
	{
		return $this->lastUsedPage;
	}

	/**
	 * @return int Number of the current pagination page.
	 */
	public function getCurrentPage(): int
	{
		return $this->page;
	}

	/**
	 * Considers the current page valid if it should not be overridden
	 * by last used of final pages and it is withing valid range.
	 * Considers item limit valid if it is within valid range.
	 * Invalid page is reset to 1.
	 * Invalid item limit is reset to its specified default value.
	 */
	private function validateItemLimitAndCurrentPage(): void
	{
		if($this->trackLastUsedPage){
			if($this->referringURLContainsAny($this->lastUsedPageUseCases))
			{
				$this->page = $this->lastUsedPage;
			}
			$this->saveCurrentPageAsLastUsed();
		}

		if($this->trackFinalPage &&
		   $this->referringURLContainsAny($this->finalPageUseCases))
		{
			$this->page = $this->getPageCount();
		}

		$this->resetItemLimitIfInvalid();
		$this->resetPageIfInvalid();
	}
	/**
	 * Checks whether the referring URL contains any string within
	 * the specified array.
	 * @param array $useCases array of strings to match against referring URL.
	 * @return bool TRUE if any string of the specified array is found within
	 * the referring URL, FALSE otherwise.
	 */
	private function referringURLContainsAny(array $useCases): bool
	{
		$referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
		foreach($useCases as $useCase){
			if(strpos($referer, $useCase) !== false){
				return true;
			}
		}
		return false;
	}

	/**
	 * Resets item limit to the default value if currently set item limit is
	 * out of valid range.
	 */
	private function resetItemLimitIfInvalid(): void
	{
		if($this->itemLimit < 1 || $this->itemLimit > $this->paginator->count())
		{
			$this->itemLimit = self::defaultItemLimit;
		}
	}

	/**
	 * Resets the current page to 1 if currently set page is out of valid range.
	 */
	private function resetPageIfInvalid(): void
	{
		if ($this->page < 1 || $this->page > $this->getPageCount())
		{
			$this->page = 1;
		}
	}

	/**
	 * @return int the number indicating, where the first element
	 * of the current pagination page is in the "array" of all
	 * queried element.
	 */
	private function getFirstElementOffset(): int
	{
		return $this->itemLimit * ($this->page - 1);
	}

	/**
	 * Saves the number of the current page session.
	 */
	private function saveCurrentPageAsLastUsed(): void
	{
		$this->session->set('last_used_page', $this->page);
	}

	/**
	 * Fetches the last used page from session.
	 */
	private function loadLastUsedPage(): void
	{
		$this->lastUsedPage = $this->session->get('last_used_page');
	}
}