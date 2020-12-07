<?php

namespace Navindex\AuthX\Models\Base;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use Navindex\AuthX\Exceptions\ModelException;

abstract class BaseModel extends Model
{
	// Field lengths
	const LENGTH_NAME = 255;

	const LENGTH_LABEL = 255;

	const LENGTH_TEXT = 65535;

	// Regex expressions
	const REGEX_NAME = '^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$';  // Regex expression for name validation

	const REGEX_PHONE = '^(?:\(?\+?[0-9]*\)?)?[0-9._\-\(\)\s\\/]*$'; // Regex expression for phone number

	// Default values
	const DEFAULT_CREATOR_ID = 1;

	const DEFAULT_DELETED = 0;

	/**
	 * The table's primary key.
	 *
	 * @var array|string
	 */
	protected $primaryKey = 'id';

	/**
	 * The table's unique keys.
	 *
	 * @var array
	 */
	protected $uniqueKeys = [];

	/**
	 * If this model should use "softDeletes" and
	 * simply set a date when rows are deleted, or
	 * do hard deletes.
	 *
	 * @var bool
	 */
	protected $useSoftDeletes = true;

	/**
	 * The column used to save soft delete state.
	 *
	 * @var string
	 */
	protected $deletedField = 'deleted';

	//--------------------------------------------------------------------

	/**
	 * Model constructor.
	 *
	 * @param ConnectionInterface $db
	 * @param ValidationInterface $validation
	 */
	public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
	{
		parent::__construct($db, $validation);

		if (\in_array('deleted', $this->allowedFields)) {
			$this->validationRules['deleted'] = $this->validationRules['deleted'] ?? 'permit_empty|in_list[0,1]';
		}

		if (\in_array('creator_id', $this->allowedFields)) {
			$this->validationRules['creator_id'] = $this->validationRules['creator_id'] ?? 'permit_empty|is_not_unique[user.id]';
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches the row of database from $this->table with a unique key
	 * matching $uniqueValue.
	 *
	 * @param array|string     $uniqueKey   Unique key column name or a key->value array
	 * @param null|array|mixed $uniqueValue One unique key or an array of unique keys
	 *
	 * @throws \App\Exceptions\ModelException
	 *
	 * @return null|array|object the resulting row of data, or null
	 */
	public function findUnique($uniqueKey, $uniqueValue = null)
	{
		if (\is_array($uniqueKey)) {
			$key = \key($uniqueKey);
			$value = $uniqueKey[$key];
		} else {
			$key = $uniqueKey;
			$value = $uniqueValue;
		}

		if ($key === $this->primaryKey) {
			return $this->find($value);
		}

		if (!\in_array($key, $this->uniqueKeys)) {
			throw ModelException::forNotUniqueKey($key, \get_class($this));
		}

		$primaryKey = $this->primaryKey;
		$this->primaryKey = $key;
		$result = $this->find($value);
		$this->primaryKey = $primaryKey;

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches the row of database from $this->table with a primary key
	 * matching $id.
	 * Changed to support composite keys.
	 *
	 * @param null|array|int|string $id One primary key or an array of primary keys,
	 *                                  or an associative array of composite keys
	 *
	 * @throws \App\Exceptions\ModelException
	 *
	 * @return null|array|object The resulting row of data, or null
	 */
	public function find($id = null)
	{
		$builder = $this->builder();

		if (true === $this->tempUseSoftDeletes) {
			$builder->where($this->table . '.' . $this->deletedField, 0);
		}

		if (\is_array($this->primaryKey)) {
			if (\array_diff_key(\array_flip($this->primaryKey), (array) $id)) {
				throw ModelException::forInvalidCompositeId(\implode(', ', $this->primaryKey), \get_class($this));
			}
			// Composite keys: the $id array keys must match the primary key array values
			$whereKeys = \array_combine($this->prefixField(\array_keys($id)), \array_values($id));
			foreach ($whereKeys as $key => $value) {
				\is_array($value) ? $builder->whereIn($key, $value) : $builder->where($key, $value);
			}
			$row = $builder->get();
			$row = $row->getResult($this->tempReturnType);
		} elseif (\is_array($id)) {
			// Single key with multiple values
			$row = $builder->whereIn($this->table . '.' . $this->primaryKey, $id)->get();
			$row = $row->getResult($this->tempReturnType);
		} elseif (\is_numeric($id) || \is_string($id)) {
			$row = $builder->where($this->table . '.' . $this->primaryKey, $id)->get();
			$row = $row->getFirstRow($this->tempReturnType);
		} else {
			$row = $builder->get();
			$row = $row->getResult($this->tempReturnType);
		}

		$eventData = $this->trigger('afterFind', ['id' => $id, 'data' => $row]);

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * Works with the current Query Builder instance to return
	 * all results, while optionally limiting them.
	 * Changed to strict deleteField.
	 *
	 * @param int $limit  The maximum number of results that will be returned
	 * @param int $offset From where to start returning data
	 *
	 * @return array Array of entities
	 */
	public function findAll(int $limit = 0, int $offset = 0)
	{
		$builder = $this->builder();

		if (true === $this->tempUseSoftDeletes) {
			$builder->where($this->table . '.' . $this->deletedField, 0);
		}

		$row = $builder->limit($limit, $offset)->get();

		$row = $row->getResult($this->tempReturnType);

		$eventData = $this->trigger('afterFind', ['data' => $row, 'limit' => $limit, 'offset' => $offset]);

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the first row of the result set. Will take any previous
	 * Query Builder calls into account when determining the result set.
	 * Changed to strict deleteField.
	 * Changed to support composite keys.
	 *
	 * @return null|array|object
	 */
	public function first()
	{
		$builder = $this->builder();

		if (true === $this->tempUseSoftDeletes) {
			$builder->where($this->table . '.' . $this->deletedField, 0);
		} else {
			if (true === $this->useSoftDeletes && empty($builder->QBGroupBy) && !empty($this->primaryKey)) {
				$builder->groupBy($this->prefixField($this->primaryKey));
			}
		}

		// Some databases, like PostgreSQL, need order
		// information to consistently return correct results.
		$orderBy = \is_array($this->primaryKey)
			? \implode(', ', $this->prefixField($this->primaryKey))
			: $this->table . '.' . $this->primaryKey;

		if (!empty($builder->QBGroupBy) && empty($builder->QBOrderBy) && !empty($this->primaryKey)) {
			$builder->orderBy($orderBy, 'asc');
		}

		$row = $builder->limit(1, 0)->get();

		$row = $row->getFirstRow($this->tempReturnType);

		$eventData = $this->trigger('afterFind', ['data' => $row]);

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a single record in $this->table. If an object is provided,
	 * it will attempt to convert it into an array.
	 *
	 * @param array|int|string $id
	 * @param array|object     $data
	 *
	 * @throws \ReflectionException
	 *
	 * @return bool
	 */
	public function update($id = null, $data = null): bool
	{
		$escape = null;

		if (\is_numeric($id) || \is_string($id)) {
			$id = [$id];
		}

		if (empty($data)) {
			$data = $this->tempData['data'] ?? null;
			$escape = $this->tempData['escape'] ?? null;
			$this->tempData = [];
		}

		if (empty($data)) {
			throw DataException::forEmptyDataset('update');
		}

		// If $data is using a custom class with public or protected
		// properties representing the table elements, we need to grab
		// them as an array.
		if (\is_object($data) && !$data instanceof \stdClass) {
			$data = static::classToArray($data, $this->primaryKey, $this->dateFormat);
		}

		// If it's still a stdClass, go ahead and convert to
		// an array so doProtectFields and other model methods
		// don't have to do special checks.
		if (\is_object($data)) {
			$data = (array) $data;
		}

		// If it's still empty here, means $data is no change or is empty object
		if (empty($data)) {
			throw DataException::forEmptyDataset('update');
		}

		// Validate data before saving.
		if (false === $this->skipValidation) {
			if (false === $this->cleanRules(true)->validate($data)) {
				return false;
			}
		}

		// Must be called first so we don't
		// strip out updated_at values.
		$data = $this->doProtectFields($data);

		if ($this->useTimestamps && !empty($this->updatedField) && !\array_key_exists($this->updatedField, $data)) {
			$data[$this->updatedField] = $this->setDate();
		}

		$eventData = $this->trigger('beforeUpdate', ['id' => $id, 'data' => $data]);

		$builder = $this->builder();

		if (\is_array($this->primaryKey) && !\array_diff_key(\array_flip($this->primaryKey), (array) $id)) {
			// Composite keys: the $id array keys must match the primary key array values
			$whereKeys = \array_combine($this->prefixField(\array_keys($id)), \array_values($id));
			foreach ($whereKeys as $key => $value) {
				\is_array($value) ? $builder->whereIn($key, $value) : $builder->where($key, $value);
			}
		} elseif ($id) {
			$builder = $builder->whereIn($this->table . '.' . $this->primaryKey, $id);
		}

		// Must use the set() method to ensure objects get converted to arrays
		$result = $builder->set($eventData['data'], '', $escape)->update();

		$this->trigger('afterUpdate', ['id' => $id, 'data' => $eventData['data'], 'result' => $result]);

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * A convenience method that will attempt to determine whether the
	 * data should be inserted or updated. Will work with either
	 * an array or object. When using with custom class objects,
	 * you must ensure that the class will provide access to the class
	 * variables, even if through a magic method.
	 * Changed to support composite keys.
	 *
	 * @param array|object $data Array or entity
	 *
	 * @throws \ReflectionException
	 *
	 * @return bool
	 */
	public function save($data): bool
	{
		if (empty($data)) {
			return true;
		}

		if (!\is_array($this->primaryKey)) {
			return parent::save($data);
		}

		// Check if all the primary key fields exist
		if (\array_diff_key(\array_flip($this->primaryKey), (array) $data)) {
			return false;
		}

		// Populate the where clouse with the composite key.
		if (\is_object($data)) {
			$primaryKey = [];
			foreach ($this->primaryKey as $key) {
				$primaryKey[$key] = $data->{$key};
			}
		} else {
			$primaryKey = \array_intersect_key($data, \array_flip($this->primaryKey));
		}

		// Check if the record exists and call the appropriate action
		if (\is_null($this->where($primaryKey)->first())) {
			$response = $this->insert($data, false);
		} else {
			$response = $this->update($data[$this->primaryKey], $data);
		}

		// Calculate the boolean response value
		if ($response instanceof BaseResult) {
			$response = false !== $response->resultID;
		} elseif (false !== $response) {
			$response = true;
		}

		return $response;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single record from $this->table where $id matches
	 * the table's primaryKey.
	 *
	 * @param null|array|int|string $id    The rows primary key(s)
	 * @param bool                  $purge Allows overriding the soft deletes setting
	 *
	 * @throws \CodeIgniter\Database\Exceptions\DatabaseException
	 * @throws \App\Exceptions\ModelException
	 *
	 * @return BaseResult|bool
	 */
	public function delete($id = null, bool $purge = false)
	{
		if (!\is_array($this->primaryKey)) {
			return parent::delete($id, $purge);
		}

		// Check if all the primary key fields exist
		if (\array_diff_key(\array_flip($this->primaryKey), (array) $id)) {
			throw ModelException::forInvalidCompositeId(\implode(', ', $this->primaryKey), \get_class($this));
		}

		$builder = $this->builder();

		$whereKeys = \array_combine($this->prefixField(\array_keys($id)), \array_values($id));
		foreach ($whereKeys as $key => $value) {
			\is_array($value) ? $builder->whereIn($key, $value) : $builder->where($key, $value);
		}

		$this->trigger('beforeDelete', ['id' => $id, 'purge' => $purge]);

		if ($this->useSoftDeletes && !$purge) {
			if (empty($builder->getCompiledQBWhere())) {
				if (CI_DEBUG) {
					throw new DatabaseException(lang('Database.deleteAllNotAllowed'));
				}
				// @codeCoverageIgnoreStart
				return false;
				// @codeCoverageIgnoreEnd
			}
			$set[$this->deletedField] = 1;

			if ($this->useTimestamps && !empty($this->updatedField)) {
				$set[$this->updatedField] = $this->setDate();
			}

			$result = $builder->update($set);
		} else {
			$result = $builder->delete();
		}

		$this->trigger('afterDelete', ['id' => $id, 'purge' => $purge, 'result' => $result, 'data' => null]);

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Permanently deletes all rows that have been marked as deleted
	 * through soft deletes (deleted = 1)
	 * Changed to strict deleteField.
	 *
	 * @return bool|mixed
	 */
	public function purgeDeleted()
	{
		if (!$this->useSoftDeletes) {
			return true;
		}

		return $this->builder()->where($this->table . '.' . $this->deletedField, 1)->delete();
	}

	//--------------------------------------------------------------------

	/**
	 * Works with the find* methods to return only the rows that
	 * have been deleted.
	 * Changed to strict deleteField.
	 *
	 * @return Model
	 */
	public function onlyDeleted()
	{
		$this->tempUseSoftDeletes = false;

		$this->builder()->where($this->table . '.' . $this->deletedField, 1);

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Override countAllResults to account for soft deleted accounts.
	 * Changed to strict deleteField.
	 *
	 * @param bool $reset
	 * @param bool $test
	 *
	 * @return mixed
	 */
	public function countAllResults(bool $reset = true, bool $test = false)
	{
		if (true === $this->tempUseSoftDeletes) {
			$this->builder()->where($this->table . '.' . $this->deletedField, 0);
		}

		// When $reset === false, the $tempUseSoftDeletes will be
		// dependant on $useSoftDeletes value because we don't
		// want to add the same "where" condition for the second time
		$this->tempUseSoftDeletes = (true === $reset)
			? $this->useSoftDeletes
			: (true === $this->useSoftDeletes
				? false
				: $this->useSoftDeletes);

		return $this->builder()->testMode($test)->countAllResults($reset);
	}

	//--------------------------------------------------------------------

	/**
	 * Processethe the result after find.
	 *
	 * @param array  $data    Trigger data structure
	 * @param string $indexBy Key to index by
	 * @param string $groupBy Key to group by
	 *
	 * @return array
	 */
	protected function processResultAfterFind(array $data, string $indexBy = null, string $groupBy = null): array
	{
		if (!empty($data['data'] ?? null)) {
			helper('array');
			if (\array_key_exists('limit', $data)) {
				foreach ($data['data'] as &$row) {
					$processedRow = $this->processRowAfterFind($row);
					if (empty($processedRow)) {
						unset($row);
					} else {
						$row = $processedRow;
					}
				}
				$data['data'] = empty($indexBy) ? $data['data'] : multi_array_order_by($indexBy, $data['data']);
				$data['data'] = empty($groupBy) ? $data['data'] : multi_array_group_by($groupBy, $data['data']);
			} else {
				$data['data'] = $this->processRowAfterFind($data['data']);
				$data['data'] = empty($groupBy) ? $data['data'] : array_group_by($groupBy, $data['data']);
			}
		}

		return $data;
	}

	//--------------------------------------------------------------------

	/**
	 * Process a record after find.
	 *
	 * @param array $row Data record
	 *
	 * @return array Processed record
	 */
	protected function processRowAfterFind($row)
	{
		return $row;
	}

	//--------------------------------------------------------------------

	/**
	 * Prepares the creator ID.
	 * Changed to use default constant.
	 *
	 * @param array $data Data to insert
	 *
	 * @return array Processed data
	 */
	protected function prepareCreator(array $data): array
	{
		if (\array_key_exists('creator_id', $data['data']) && empty($data['data']['creator_id'])) {
			$data['data']['creator_id'] = self::DEFAULT_CREATOR_ID;
		}

		return $data;
	}

	//--------------------------------------------------------------------

	/**
	 * Prepares the 'deleted' field.
	 * Changed to use default constant.
	 *
	 * @param array $data Data to insert
	 *
	 * @return array Processed data
	 */
	protected function prepareDeleted(array $data): array
	{
		if (\array_key_exists('deleted', $data['data']) && empty($data['data']['deleted'])) {
			$data['data']['deleted'] = self::DEFAULT_DELETED;
		}

		return $data;
	}

	//--------------------------------------------------------------------

	/**
	 * Prefix a path with another.
	 *
	 * @param null|array|object $row
	 *
	 * @return null|string
	 */
	protected function addPath($row, string $fileKey, string $folderKey = 'folder'): ?string
	{
		if (\is_array($row) && !empty($row[$fileKey])) {
			return (!empty($row[$folderKey]) ? $row[$folderKey] . DIRECTORY_SEPARATOR : '') . $row[$fileKey];
		}

		return null;
	}

	//--------------------------------------------------------------------

	/**
	 * Prefix the field name(s) with a string or the table name.
	 *
	 * @param string/array $field  Field name or array of field names
	 * @param null|string  $prefix String or null for table name
	 *
	 * @return array|string
	 */
	protected function prefixField($field, string $prefix = null)
	{
		$prefix = \rtrim(($prefix ?? $this->table), '.') . '.';

		if (\is_array($field)) {
			\array_walk($field, function (&$value, $key) use ($prefix) {
				$value = $prefix . $value;
			});

			return $field;
		}

		return $prefix . $field;
	}
}
