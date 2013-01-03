<?php
/**
 * MySQL classes for generating xPDOObject classes and maps from an xPDO schema.
 *
 * @package xpdo
 * @subpackage om.mysql
 */

/**
 * Include the parent {@link xPDOGenerator} class.
 */
include_once(XPDO_CORE_PATH . 'om/' . $modx->config['dbtype'] . '/xpdogenerator.class.php');

/**
 * An extension for generating {@link xPDOObject} class and map files for MySQL.
 *
 * A MySQL-specific extension to an {@link xPDOManager} instance that can
 * generate class stub and meta-data map files from a provided XML schema of a
 * database structure.
 *
 * @package xpdo
 * @subpackage om.mysql
 */
class my_xPDOGenerator_mysql extends xPDOGenerator_mysql {
    /**
     * an array of allowed tables
     * @var array
     */
    protected $allowed_tables;
    
    /**
     * active data base to connect to
     * @var (String) $database
     */
    protected $databaseName;
    
    /**
     * set the database
     * 
     */
    public function setDatabase($database=NULL) {
        if (empty($database) ) {
            $this->databaseName = $this->manager->xpdo->escape($this->manager->xpdo->config['dbname']);
        } else {
            $this->databaseName = $database;
        }
    }
    
    /**
     * set the allowed tables
     * 
     */
    public function setAllowedTables(array $tables=array()) {
        $this->allowed_tables = $tables;
        /*
        echo '<br>Table Array: ';
        print_r($tables);
        echo '<br>';
        */
    }
    /**
     * This only generates scheme files for specified tables rather then the entire database
     * 
     * Write an xPDO XML Schema from your database.
     *
     * @param string $schemaFile The name (including path) of the schemaFile you
     * want to write.
     * @param string $package Name of the package to generate the classes in.
     * @param string $baseClass The class which all classes in the package will
     * extend; by default this is set to {@link xPDOObject} and any
     * auto_increment fields with the column name 'id' will extend {@link
     * xPDOSimpleObject} automatically.
     * @param string $tablePrefix The table prefix for the current connection,
     * which will be removed from all of the generated class and table names.
     * Specify a prefix when creating a new {@link xPDO} instance to recreate
     * the tables with the same prefix, but still use the generic class names.
     * @param boolean $restrictPrefix Only reverse-engineer tables that have the
     * specified tablePrefix; if tablePrefix is empty, this is ignored.
     * @return boolean True on success, false on failure.
     */
    public function writeTableSchema($schemaFile, $package= '', $baseClass= '', $tablePrefix= '', $restrictPrefix= false) {
        if (empty ($package))
            $package= $this->manager->xpdo->package;
        if (empty ($baseClass))
            $baseClass= 'xPDOObject';
        if (empty ($tablePrefix))
            $tablePrefix= $this->manager->xpdo->config[xPDO::OPT_TABLE_PREFIX];
        $schemaVersion = xPDO::SCHEMA_VERSION;
        $xmlContent = array();
        $xmlContent[] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xmlContent[] = "<model package=\"{$package}\" baseClass=\"{$baseClass}\" platform=\"mysql\" defaultEngine=\"MyISAM\" version=\"{$schemaVersion}\">";
        //read list of tables
        $dbname = $this->databaseName; 
        //$this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Database name: ' . $dbname);
        $tableLike= ($tablePrefix && $restrictPrefix) ? " LIKE '{$tablePrefix}%'" : '';
        $tablesStmt= $this->manager->xpdo->prepare("SHOW TABLES FROM {$dbname}{$tableLike}");
        $tablesStmt->execute();
        $tables= $tablesStmt->fetchAll(PDO::FETCH_NUM);
        if ($this->manager->xpdo->getDebug() === true) {
            $this->manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, print_r($tables, true));
        }
        foreach ($tables as $table) {
            $xmlObject= array();
            $xmlFields= array();
            $xmlIndices= array();
            // the only thing added to this function the rest is copied:
            if ( !in_array($table[0],$this->allowed_tables) ) {
                //echo '<br>No Table: '.$table[0];
                //$this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'CMPGenerator->my_oPDO0->writeTableSchema -> No Table: '.$table[0]);
                continue;
            }
            //echo '<br>Table: '. $table[0];
            //$this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'CMPGenerator->my_oPDO0->writeTableSchema -> Table: '.$table[0].' - Pre: '.$tablePrefix.' - Restrict: '.$restrictPrefix );
            
            // End custom
            if (!$tableName= $this->getTableName($table[0], $tablePrefix, $restrictPrefix)) {
                continue;
            }
            $class= $this->getClassName($tableName);
            $extends= $baseClass;
            $sql = 'SHOW COLUMNS FROM '.$this->manager->xpdo->escape($dbname).'.'.$this->manager->xpdo->escape($table[0]);
            //$this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Line: '.__LINE__.' Sql: '.$sql);
            $fieldsStmt= $this->manager->xpdo->query($sql);
            if ($fieldsStmt) {
                $fields= $fieldsStmt->fetchAll(PDO::FETCH_ASSOC);
                if ($this->manager->xpdo->getDebug() === true) $this->manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, print_r($fields, true));
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        $Field= '';
                        $Type= '';
                        $Null= '';
                        $Key= '';
                        $Default= '';
                        $Extra= '';
                        extract($field, EXTR_OVERWRITE);
                        $Type= xPDO :: escSplit(' ', $Type, "'", 2);
                        $precisionPos= strpos($Type[0], '(');
                        $dbType= $precisionPos? substr($Type[0], 0, $precisionPos): $Type[0];
                        $dbType= strtolower($dbType);
                        $Precision= $precisionPos? substr($Type[0], $precisionPos + 1, strrpos($Type[0], ')') - ($precisionPos + 1)): '';
                        if (!empty ($Precision)) {
                            $Precision= ' precision="' . trim($Precision) . '"';
                        }
                        $attributes= '';
                        if (isset ($Type[1]) && !empty ($Type[1]) ) {
                            $attributes= ' attributes="' . trim($Type[1]) . '"';
                        }
                        $PhpType= $this->manager->xpdo->driver->getPhpType($dbType);
                        $Null= ' null="' . (($Null === 'NO') ? 'false' : 'true') . '"';
                        $Key= $this->getIndex($Key);
                        $Default= $this->getDefault($Default);
                        if (!empty ($Extra)) {
                            if ($Extra === 'auto_increment') {
                                if ($baseClass === 'xPDOObject' && $Field === 'id') {
                                    $extends= 'xPDOSimpleObject';
                                    continue;
                                } else {
                                    $Extra= ' generated="native"';
                                }
                            } else {
                                $Extra= ' extra="' . strtolower($Extra) . '"';
                            }
                            $Extra= ' ' . $Extra;
                        }
                        $xmlFields[] = "\t\t<field key=\"{$Field}\" dbtype=\"{$dbType}\"{$Precision}{$attributes} phptype=\"{$PhpType}\"{$Null}{$Default}{$Key}{$Extra} />";
                    }
                } else {
                    $this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'No columns were found in table ' .  $table[0]);
                }
            } else {
                $this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Error retrieving columns for table ' .  $table[0]);
            }
            $whereClause= ($extends === 'xPDOSimpleObject' ? " WHERE `Key_name` != 'PRIMARY'" : '');
            $indexesStmt= $this->manager->xpdo->query('SHOW INDEXES FROM '.$this->manager->xpdo->escape($dbname).'.'.$this->manager->xpdo->escape($table[0]) . $whereClause);
            if ($indexesStmt) {
                $indexes= $indexesStmt->fetchAll(PDO::FETCH_ASSOC);
                if ($this->manager->xpdo->getDebug() === true) $this->manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Indices for table {$table[0]}: " . print_r($indexes, true));
                if (!empty($indexes)) {
                    $indices = array();
                    foreach ($indexes as $index) {
                        if (!array_key_exists($index['Key_name'], $indices)) $indices[$index['Key_name']] = array();
                        $indices[$index['Key_name']][$index['Seq_in_index']] = $index;
                    }
                    foreach ($indices as $index) {
                        $xmlIndexCols = array();
                        if ($this->manager->xpdo->getDebug() === true) $this->manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Details of index: " . print_r($index, true));
                        foreach ($index as $columnSeq => $column) {
                            if ($columnSeq == 1) {
                                $keyName = $column['Key_name'];
                                $primary = $keyName == 'PRIMARY' ? 'true' : 'false';
                                $unique = empty($column['Non_unique']) ? 'true' : 'false';
                                $packed = empty($column['Packed']) ? 'false' : 'true';
                                $type = $column['Index_type'];
                            }
                            $null = $column['Null'] == 'YES' ? 'true' : 'false';
                            $xmlIndexCols[]= "\t\t\t<column key=\"{$column['Column_name']}\" length=\"{$column['Sub_part']}\" collation=\"{$column['Collation']}\" null=\"{$null}\" />";
                        }
                        $xmlIndices[]= "\t\t<index alias=\"{$keyName}\" name=\"{$keyName}\" primary=\"{$primary}\" unique=\"{$unique}\" type=\"{$type}\" >";
                        $xmlIndices[]= implode("\n", $xmlIndexCols);
                        $xmlIndices[]= "\t\t</index>";
                    }
                } else {
                    $this->manager->xpdo->log(xPDO::LOG_LEVEL_WARN, 'No indexes were found in table ' .  $table[0]);
                }
            } else {
                $this->manager->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Error getting indexes for table ' .  $table[0]);
            }
            $xmlObject[] = "\t<object class=\"{$class}\" table=\"{$tableName}\" extends=\"{$extends}\">";
            $xmlObject[] = implode("\n", $xmlFields);
            if (!empty($xmlIndices)) {
                $xmlObject[] = '';
                $xmlObject[] = implode("\n", $xmlIndices);
            }
            $xmlObject[] = "\t</object>";
            $xmlContent[] = implode("\n", $xmlObject);
        }
        $xmlContent[] = "</model>";
        if ($this->manager->xpdo->getDebug() === true) {
           $this->manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, implode("\n", $xmlContent));
        }
        $file= fopen($schemaFile, 'wb');
        $written= fwrite($file, implode("\n", $xmlContent));
        fclose($file);
        return true;
    }
}
