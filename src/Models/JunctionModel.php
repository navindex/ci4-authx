<?php

namespace App\Models\Base;

use App\Models\Base\BaseModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

/**
 * Model representing a mny-to-many connector table.
 */
abstract class JunctionModel extends BaseModel
{
	const LEFT = 0, RIGHT = 1;

	/**
	 * The table's composite primary key,
	 * formed of the left and right foreign keys.
	 *
	 * @var array
	 */
	protected $primaryKey = [null, null];

	/**
	 * The left and right tables.
	 *
	 * @var array
	 */
	protected $tables = [null, null];

	protected $allowedFields = [
		'deleted',
		'creator_id',
	];

	protected $validationRules = [];

	//--------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param ConnectionInterface $db         Database connection class
	 * @param ValidationInterface $validation Validation class
	 */
	public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
	{
		parent::__construct($db, $validation);

		if (!empty($this->tables[static::LEFT])) {
			$leftId = $this->tables[static::LEFT] . '_id';

			$this->primaryKey[static::LEFT] = $this->primaryKey[static::LEFT] ?? $leftId ?? null;

			if (!in_array($leftId, $this->allowedFields)) {
				$this->allowedFields[] = $leftId;
			}

			$this->validationRules[$leftId] = 'is_not_unique[' . $this->tables[static::LEFT] . '.id]';
		}

		if (!empty($this->tables[static::RIGHT])) {
			$rightId = $this->tables[static::RIGHT] . '_id';

			$this->primaryKey[static::RIGHT] = $this->primaryKey[static::RIGHT] ?? $rightId ?? null;

			if (!in_array($rightId, $this->allowedFields)) {
				$this->allowedFields[] = $rightId;
			}

			$this->validationRules[$rightId] = 'is_not_unique[' . $this->tables[static::RIGHT] . '.id]';
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a new record.
	 *
	 * @param int $leftId  Left ID
	 * @param int $rightId Right ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function connect(int $leftId, int $rightId): bool
	{
		return false !== $this->save(array_combine($this->primaryKey, [$leftId, $rightId]));
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a record.
	 *
	 * @param int $leftId  Left ID
	 * @param int $rightId Right ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function disconnect(int $leftId, int $rightId): bool
	{
		return false !== $this->where(array_combine($this->primaryKey, [$leftId, $rightId]))->delete();
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes all records with a given Left ID.
	 *
	 * @param int $leftId  Left ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deleteLeft(int $leftId): bool
	{
		return false !== $this->where($this->primaryKey[0], $leftId)->delete();
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes all records with a given Right ID.
	 *
	 * @param int $rightId Right ID
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deleteRight(int $rightId): bool
	{
		return false !== $this->where($this->primaryKey[1], $rightId)->delete();
	}
}
