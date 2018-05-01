<?php

namespace App\Services;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationResult implements \Iterator
{
	private $count;
	private $totalCount;

	private $items;
	private $iteratorPosition = 0;

	/**
	 * PaginationResults constructor.
	 * @param Paginator $paginator
	 */
	public function __construct(Paginator $paginator)
	{
		$this->items = $paginator->getQuery()->getResult();
		$this->totalCount = $paginator->count();
		$this->count = \count($this->items);
	}

	public function get(int $index)
	{
		return $this->items[$index];
	}

	/**
	 * @return int The number of items fetched for
	 * the current page.
	 */
	public function getCount(): int
	{
		return $this->count;
	}

	/**
	 * @return int The total number of items found.
	 */
	public function getTotalCount(): int
	{
		return $this->totalCount;
	}

	/**
	 * Return the current element
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		return $this->items[$this->iteratorPosition];
	}

	/**
	 * Move forward to next element
	 * @return void Any returned value is ignored.
	 */
	public function next(): void
	{
		++$this->iteratorPosition;
	}

	/**
	 * Return the key of the current element
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		return $this->iteratorPosition;
	}

	/**
	 * Checks if current position is valid
	 * @return boolean The return value will be cast to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid(): bool
	{
		return isset($this->items[$this->iteratorPosition]);
	}

	/**
	 * Rewind the Iterator to the first element
	 * @return void Any returned value is ignored.
	 */
	public function rewind(): void
	{
		$this->iteratorPosition = 0;
	}
}