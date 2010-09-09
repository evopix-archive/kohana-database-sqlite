<?php defined('SYSPATH') or die('No direct script access.');
/**
 * SQLite database connection.
 *
 * @author     Brandon Summers
 * @copyright  (c) 2010 Brandon Summers
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_SQLite extends Database_PDO {

	// SQLite does not support SET NAMES
	public function set_charset($charset) { }
	
	public function list_tables($like = NULL)
	{
		if (is_string($like))
		{
			// Search for table names
			$result = $this->query(Database::SELECT, 'SELECT name FROM SQLITE_MASTER WHERE type="table" AND name LIKE '.$this->quote($like).' ORDER BY name', FALSE);
		}
		else
		{
			// Find all table names
			$result = $this->query(Database::SELECT, 'SELECT name FROM SQLITE_MASTER WHERE type="table" ORDER BY name', FALSE);
		}

		$tables = array();
		foreach ($result as $row)
		{
			// Get the table name from the results
			$tables[] = reset($row);
		}

		return $tables;
	}

	public function list_columns($table, $like = NULL)
	{
		if (is_string($like))
		{
			throw new Kohana_Exception('Database method :method  with LIKE param is not supported by :class',
				array(':method' => __FUNCTION__, ':class' => __CLASS__));
		}

		// Find all column names
		$result = $this->query(Database::SELECT, 'PRAGMA table_info('.$table.')', FALSE);
		
		$count = 0;
		$columns = array();

		foreach ($result as $row)
		{
			list($type, $length) = $this->_parse_type($row['type']);

			$column = $this->datatype($type);

			$column['column_name']      = $row['name'];
			$column['column_default']   = $row['dflt_value'];
			$column['data_type']        = $type;
			$column['is_nullable']      = ($row['notnull'] == '0');
			$column['ordinal_position'] = ++$count;

			$columns[$row['name']] = $column;
		}

		return $columns;
	}

	public function datatype($type)
	{
		// SQLite data types are uppercase
		$type = strtolower($type);

		return parent::datatype($type);
	}

} // End Database_SQLite
