<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

trait Model_Prepare_Trait {

	//
	//    https://gist.github.com/diolemo/2b41830e89d41d0fe328
	// 
	//    {{ <fields> AS <alias> [USING <model>] }}
	//
	//    <fields> = table.*                  # select all fields from table
	//    <fields> = alias.*                  # select all fields from table or sub-query (using the alias)
	//    <fields> = a.a1, a.a2, a.a3         # select specific fields from a table/sub-query 
	//    <fields> = a.*, b.b3, b.b4          # mix and match fields from 2 tables/sub-queries
	//
	//    <alias> = example                   # the property name for the object ($model->example ==> Model_Example)
	//    <model> = Model_Example             # the model class to use - optional, defaults to model_<alias>
	//

	public static function prepare($sql)
	{
		$sql = static::prepare_prefixes($sql);
		return $sql;
	}

	protected static function prepare_prefixes($sql)
	{
		// ---------------------------------
		// ---------------------------------

		$inject_rx = '/

			\{\{\s*                         # opening brackets 
			((                              # > FIELDS block
				(                            #   > FIELD_NAME block
					(([a-z0-9_]+)\.)          #     - table or table alias
					([a-z0-9_\*]+)            #     - field name or wildcard
				)                            #   < FIELD_NAME block
				(\s*,\s*)*                   #   - FIELD separator
			)+)\s+                          # < FIELDS block
			AS\s+                           # AS keyword
			([a-z0-9_]+)\s+                 # AS value
			(                               # > USING block
				USING\s+                     #   - USING keyword
				([a-z0-9_]+)\s+              #   - USING value
			)?                              # < USING block
			\}\}                            # closing brackets

		/isx';

		// ---------------------------------
		// ---------------------------------

		$field_rx = '/

			(([a-z0-9_]+)\.)      # table or table alias
			([a-z0-9_\*]+)        # field name or wildcard

		/isx';

		// ---------------------------------
		// ---------------------------------

		$field_separator_rx = '/

			(\s*,\s*)+     # field separator

		/isx';

		// ---------------------------------
		// ---------------------------------

		$protected_rxs = array(

			// double quoted str
			'#".*?(?<!\\\\)"#s',   

			// single quoted str
			'#\'.*?(?<!\\\\)\'#s', 

		);

		$strlen = strlen($sql);
		$offset = 0;

		while ($offset < $strlen)
		{
			// test for injection syntax
			if (!preg_match($inject_rx, $sql, $inj_match, 
				PREG_OFFSET_CAPTURE, $offset))
				break;

			// test for quoted string 
			// before the injection 
			$inj_pos = $inj_match[0][1];
			foreach ($protected_rxs as $rx)
			{
				if (preg_match($rx, $sql, $pro_match, 
					PREG_OFFSET_CAPTURE, $offset) && 
					$pro_match[0][1] < $inj_pos)
				{
					$len = strlen($pro_match[0][0]);
					$pos = $pro_match[0][1];
					$offset = $pos + $len;
					continue 2;
				}
			}
			
			$len = strlen($inj_match[0][0]);
			$pos = $inj_match[0][1];

			$fields = array();
			$field_str_arr = preg_split($field_separator_rx, $inj_match[1][0]);

			$alias_name = $inj_match[8][0];
			$model_name = empty($inj_match[10][0])
				? sprintf('model_%s', $alias_name)
				: $inj_match[10][0];

			if ($model_name === sprintf('model_%s', $alias_name))
			     $model_alias_match = true;
			else $model_alias_match = false;

			if (!class_exists($model_name))
			{
				$message = sprintf('Model not found: %s', $model_name);
				throw new Exception($message);
			}
			
			foreach ($field_str_arr as $field_str)
			{
				if (preg_match($field_rx, $field_str, $match_field))
				{
					$table = $match_field[2];
					$column = $match_field[3];

					if ($column === '*')
					{
						foreach ($model_name::__fields() as $column)
						{
							$fields[] = (object) array(
								'table' => $table,
								'column' => $column,
							);
						}
					}
					else
					{
						$fields[] = (object) array(
							'table' => $table,
							'column' => $column,
						);
					}
				}
			}

			$sql_before = substr($sql, 0, $pos);
			$sql_after  = substr($sql, ($pos + $len));
			$sql_inject = array();

			foreach ($fields as $field)
			{
				$table = backtick($field->table);
				$column = backtick($field->column);
				$alias = backtick(sprintf('$%s$%s$%s', 
					$model_alias_match ? null : $model_name, 
					$alias_name, 
					$field->column
				));

				$sql_inject[] = sprintf('%s.%s as %s', 
					$table, $column, $alias);
			}

			$sql_inject = comma_separate($sql_inject, PHP_EOL);
			$sql = concat($sql_before, $sql_inject, $sql_after);
		}

		return $sql;
	}

}