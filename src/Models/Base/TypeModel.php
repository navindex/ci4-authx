<?php

namespace Navindex\Auth\Models\Base;

use Navindex\Auth\Entities\Type;
use Navindex\Auth\Models\Base\BaseModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

abstract class TypeModel extends BaseModel
{
	protected $primaryKey = 'id';

	protected $uniqueKeys = ['name'];

	protected $returnType = Type::class;

	protected $allowedFields = [
		'name',
		'label',
		'deleted',
		'creator_id',
	];

	protected $validationRules = [
		'name'  => 'required|max_length[' . self::LENGTH_NAME . ']|alpha_dash',
		'label' => 'required|max_length[' . self::LENGTH_LABEL . ']|alpha_numeric_punct',
	];

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
		$this->validationRules['name'] .= '|is_unique[' . $this->table . '.name,id,{id}]';
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the type record.
	 *
	 * @param int|string $type Type ID or name
	 *
	 * @return null|\App\Entities\Type Type record
	 */
	public function getType($type): ?Type
	{
		if (is_numeric($type)) {
			return $this->find($type);
		}

		if (is_string($type)) {
			return $this->findUnique('name', $type);
		}

		return null;
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the type ID.
	 *
	 * @param int|string $type Type ID or name
	 *
	 * @return null|int Type ID or null
	 */
	public function getTypeId($type): ?int
	{
		return ($this->getType($type))->id ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the type name.
	 *
	 * @param int|string $type Type ID or name
	 *
	 * @return null|string Type name or null
	 */
	public function getTypeName($type): ?string
	{
		return ($this->getType($type))->name ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a new type.
	 *
	 * @param string $name  Type name
	 * @param string $label Type label
	 *
	 * @return false|int The new type ID or false
	 */
	public function addType(string $name, string $label)
	{
		return $this->insert(['name' => $name, 'label' => $label]);
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a type.
	 *
	 * @param int    $id    Type ID
	 * @param string $name  Type name
	 * @param string $label Type label
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function updateType(int $id, string $name, string $label): bool
	{
		return $this->update($id, ['name' => $name, 'label' => $label]);
	}

	//--------------------------------------------------------------------

	/**
	 * A convenience method that will attempt to determine whether the
	 * type record should be inserted or updated.
	 *
	 * @param int    $id    Type ID
	 * @param string $name  Type name
	 * @param string $label Type label
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function saveType(int $id, string $name, string $label): bool
	{
		return $this->save($id, ['id' => $id, 'name' => $name, 'label' => $label]);
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a type.
	 *
	 * @param int|string $type Type ID or name
	 *
	 * @return bool True if the operation was successful, false otherwise
	 */
	public function deleteType($type)
	{
		$id = $this->getTypeId($type);

		if (is_numeric($id)) {
			return false !== $this->delete($id);
		}

		return false;
	}
}
