<?php

namespace Navindex\AuthX\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuthMigration extends Migration
{
	/**
	 * Perform a migration step.
	 */
	public function up()
	{
		// Attempt type table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique attempt type identifier (surrogate key)',
				], 'name' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique attempt type name (used by the application)',
				], 'label' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique attempt type label (used by the front end)',
				],
			])
			->addPrimaryKey('id')
			->addUniqueKey('name')
			->addUniqueKey('label')
			->createTable('attempt_type', true, [
				'comment' => 'Attempt types',
			])
		;

		// Permission table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique permission identifier (surrogate key)',
				], 'name' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique permission name (used by the application)',
				], 'label' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique permission label (used by the front end)',
				],
			])
			->addPrimaryKey('id')
			->addUniqueKey('name')
			->addUniqueKey('label')
			->createTable('permission', true, [
				'comment' => 'Permissions',
			])
		;

		// Role table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique role identifier (surrogate key)',
				], 'name' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique role name (used by the application)',
				], 'label' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique role label (used by the front end)',
				],
			])
			->addPrimaryKey('id')
			->addUniqueKey('name')
			->addUniqueKey('label')
			->createTable('role', true, [
				'comment' => 'Roles',
			])
		;

		// User status table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique status identifier (surrogate key)',
				], 'name' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique status name (used by the application)',
				], 'label' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Unique status label (used by the front end)',
				],
			])
			->addPrimaryKey('id')
			->addUniqueKey('name')
			->addUniqueKey('label')
			->createTable('status', true, [
				'comment' => 'User statuses',
			])
		;

		/*
		 * Attempt table.
		 *
		 * @todo Check if 'success' can be NULL or not.
		 */
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique attempt identifier (surrogate key)',
				], 'type_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'null'       => false,
					'comment'    => 'Attempt type',
				], 'user_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'null'       => true,
					'comment'    => 'User of the successful attempt',
				], 'success' => [
					'type'       => 'tinyint',
					'constraint' => 1,
					'null'       => true,
					'comment'    => 'Was the attempt successful?',
				], 'ipv4' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'null'       => true,
					'comment'    => 'IPv4 address',
				], 'ipv6' => [
					'type'       => 'binary',
					'constraint' => 16,
					'null'       => true,
					'comment'    => 'IPv6 address',
				], 'user_agent' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'User agent',
				], 'email' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Email address',
				], 'token' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Token',
				], 'captured_at'  => [
					'type'    => 'datetime',
					'null'    => true,
					'comment' => 'The UTC date and time when the attempt was captured',
				],
			])
			->addPrimaryKey('id')
			->addKey('type_id')
			->addKey('user_id')
			->addKey('email')
			->createTable('attempt', true, [
				'comment' => 'Attempts',
			])
		;

		// User table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique user identifier (surrogate key)',
				], 'email' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Email address',
				], 'username' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Username',
				], 'password_hash' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'User password hash',
				], 'reset_hash' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Hash value to reset password',
				], 'activate_hash' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Hash to activate the user',
				], 'reset_requested_at' => [
					'type'    => 'datetime',
					'null'    => true,
					'comment' => 'The UTC date and time when the password reset was requested',
				], 'reset_expires_at' => [
					'type'    => 'datetime',
					'null'    => true,
					'comment' => 'The UTC date and time when the password reset request expires',
				], 'status_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'null'       => false,
					'comment'    => 'User status',
				], 'status_reason' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Status message (e.g. reason)',
				], 'force_pass_reset' => [
					'type'       => 'tinyint',
					'constraint' => 1,
					'null'       => false,
					'default'    => 0,
					'comment'    => 'Is the user forced to change password at the next login?',
				],
			])
			->addPrimaryKey('id')
			->addUniqueKey('email')
			->addUniqueKey('username')
			->addKey('status_id')
			->createTable('user', true, [
				'comment' => 'Users',
			])
		;

		/*
		 * Persistence token table.
		 *
		 * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
		 */
		$this->addMetaFields();
		$this->forge
			->addField([
				'id' => [
					'type'           => 'int',
					'constraint'     => 10,
					'unsigned'       => true,
					'auto_increment' => true,
					'comment'        => 'Unique persistence token identifier (surrogate key)',
				], 'user_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'null'       => false,
					'comment'    => 'The user that the persistence token belongs to',
				], 'selector' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => false,
					'comment'    => 'Selector device',
				], 'validator_hash' => [
					'type'       => 'varchar',
					'constraint' => 255,
					'null'       => true,
					'comment'    => 'Hashed token validator',
				], 'expires_at' => [
					'type'    => 'datetime',
					'null'    => false,
					'comment' => 'The UTC date and time when the persistence token expires',
				],
			])
			->addPrimaryKey('id')
			->addKey('user_id')
			->addKey('selector')
			->addForeignKey('user_id', 'user', 'id', false, 'CASCADE')
			->createTable('persistence', true, [
				'comment' => 'Persistence tokens',
			])
		;

		// Role permission junction table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'role_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'Role',
				], 'permission_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'Permission',
				],
			])
			->addPrimaryKey('role_id', 'permission_id')
			->addKey('permission_id')
			->addForeignKey('role_id', 'role', 'id', false, 'CASCADE')
			->addForeignKey('permission_id', 'permission', 'id', false, 'CASCADE')
			->createTable('role_permission', true, [
				'comment' => 'Role permissions',
			])
		;

		// User permission junction table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'user_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'User',
				], 'permission_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'Permission',
				],
			])
			->addPrimaryKey('user_id', 'permission_id')
			->addKey('permission_id')
			->addForeignKey('user_id', 'user', 'id', false, 'CASCADE')
			->addForeignKey('permission_id', 'permission', 'id', false, 'CASCADE')
			->createTable('user_permission', true, [
				'comment' => 'User permissions',
			])
		;

		// User role junction table.
		$this->addMetaFields();
		$this->forge
			->addField([
				'user_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'User',
				], 'role_id' => [
					'type'       => 'int',
					'constraint' => 10,
					'unsigned'   => true,
					'comment'    => 'Permission',
				],
			])
			->addPrimaryKey('user_id', 'role_id')
			->addKey('role_id')
			->addForeignKey('user_id', 'user', 'id', false, 'CASCADE')
			->addForeignKey('role_id', 'role', 'id', false, 'CASCADE')
			->createTable('user_role', true, [
				'comment' => 'User roles',
			])
		;
	}

	//--------------------------------------------------------------------

	public function down()
	{
		// Keep this order to prevent blocking constraints.
		$this->forge->dropTable('role_permission', true);
		$this->forge->dropTable('user_permission', true);
		$this->forge->dropTable('user_role', true);
		$this->forge->dropTable('persistence', true);
		$this->forge->dropTable('attempt', true);
		$this->forge->dropTable('user', true);
		$this->forge->dropTable('attempt_type', true);
		$this->forge->dropTable('permission', true);
		$this->forge->dropTable('role', true);
		$this->forge->dropTable('status', true);
	}

	//--------------------------------------------------------------------

	/**
	 * Adds the metadat columns.
	 */
	private function addMetaFields()
	{
		$this->forge->addField([
			'created_at'  => [
				'type'    => 'datetime',
				'null'    => false,
				'comment' => 'The UTC date and time when this record was created',
			], 'updated_at'  => [
				'type'    => 'datetime',
				'null'    => true,
				'comment' => 'The UTC date and time when this record was last updated',
			], 'deleted_at'  => [
				'type'    => 'datetime',
				'null'    => true,
				'comment' => 'The UTC date and time when this record was deleted',
			],
		]);
	}
}
