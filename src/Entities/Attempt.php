<?php

namespace Navindex\Auth\Entities;

use Navindex\Auth\Entities\BaseEntity;
use Navindex\Auth\Models\Types\AttemptTypeModel;

class Attempt extends BaseEntity
{
	/**
	 * Define properties that are automatically converted to Time instances.
	 */
	protected $dates = ['captured_at'];

	/**
	 * Array of field names and the type of value to cast them as
	 * when they are accessed.
	 */
	protected $casts = [
		'id'          => 'integer',
		'type_id'     => 'integer',
		'captured_at' => 'datetime',
		'success'     => '?bool',
		'ipv4'        => '?integer',
		'ipv6'        => '?string',
		'user_agent'  => '?string',
		'email'       => '?string',
		'user_id'     => '?integer',
		'token'       => '?string',
		'deleted'     => 'boolean',
		'creator_id'  => 'integer',
	];

	/**
	 * Maps names used in sets and gets against unique
	 * names within the class, allowing independence from
	 * database column names.
	 *
	 * Example:
	 *  $datamap = [
	 *      'db_name' => 'class_name'
	 *  ];
	 *
	 * @var array
	 */
	protected $datamap = [
		'type' => 'type_id',
	];

	/**
	 * The list of available attempt types.
	 * id => name.
	 *
	 * @var array
	 */
	protected $attemptTypes = [];

	//--------------------------------------------------------------------

	/**
	 * Automatically converts the IPv4 address when set.
	 *
	 * @param null|string $ipv4 IPv4 address
	 */
	public function setIpv4(string $ipv4 = null): void
	{
		$this->attributes['ipv4'] = \ip2long($ipv4);
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically converts to IPv4 address.
	 *
	 * @return string IPv4 address
	 */
	public function getIpv4(): string
	{
		return \long2ip($this->attributes['ipv4']);
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically converts the IPv6 address when set.
	 *
	 * @param null|string $ipv6 IPv6 address
	 */
	public function setIpv6(string $ipv6 = null): void
	{
		$this->attributes['ipv6'] = empty($ipv6)
			? null
			: \inet_pton($ipv6) ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically converts to IPv6 address.
	 *
	 * @return string IPv6 address
	 */
	public function getIpv6(): string
	{
		return \inet_ntop($this->attributes['ipv6']);
	}

	//--------------------------------------------------------------------

	/**
	 * Automatically converts the attempt type name to ID.
	 *
	 * @param string $type Attempt type name
	 */
	public function setType(string $type): void
	{
		if (\is_null($type)) {
			$this->attributes['type_id'] = null;
		} else {
			$list = $this->getAttemptTypes();
			$this->attributes['type_id'] = \array_search(\strtolower($type), $list) ?? null;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Converts the attempt type ID to name.
	 *
	 * @return null|string Attempt type name
	 */
	public function getType(): ?string
	{
		if (empty($this->attributes['type_id'])) {
			return null;
		}

		$list = $this->getAttemptTypes();

		return $list[$this->attributes['type_id']] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns all available attempt types, formatted for simple checking:.
	 *
	 * [
	 *    id => name,
	 *    id => name,
	 * ]
	 *
	 * @return array|mixed
	 */
	protected function getAttemptTypes()
	{
		if (empty($this->attemptTypes)) {
			$list = model(AttemptTypeModel::class)->findAll();
			foreach ($list as $attempt) {
				$this->attemptTypes[$attempt->id] = \strtolower($attempt->name);
			}
		}

		return $this->attemptTypes;
	}
}
