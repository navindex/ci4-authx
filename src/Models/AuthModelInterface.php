<?php

namespace Navindex\Auth\Models;

interface AuthModelInterface
{
	/**
	 * Grabs the last error(s) that occurred. If data was validated,
	 * it will first check for errors there, otherwise will try to
	 * grab the last error from the Database connection.
	 *
	 * @param boolean $forceDB Always grab the db error, not validation
	 *
	 * @return null|array Array of errors or null
	 */
	public function errors(bool $forceDB = false);

	//--------------------------------------------------------------------

	/**
	 * Retrieves all available entities.
	 *
	 * @return array Array of records
	 */
	public function fetchAll(): array;

	//--------------------------------------------------------------------

	/**
	 * Retrieves a single record.
	 *
	 * @param int|string $record Record ID or name
	 *
	 * @return null|object Record object or null
	 */
	public function fetch($record);

	//--------------------------------------------------------------------

	/**
	 * Retrieves the record ID.
	 *
	 * @param int|string $record Record ID or name
	 *
	 * @return null|int Record ID or null
	 */
	public function getId($record): ?int;

	//--------------------------------------------------------------------
	/**
	 * Retrieves the record name.
	 *
	 * @param int|string $record Record ID or name
	 *
	 * @return null|string Record name or null
	 */
	public function getName($record): ?string;

	//--------------------------------------------------------------------

	/**
	 * Deletes a record.
	 *
	 * @param int|string $record Record ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function remove($record): bool;
}
