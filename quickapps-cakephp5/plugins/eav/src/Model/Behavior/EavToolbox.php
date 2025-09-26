<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since    2.0.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://www.quickappscms.org
 * @license  http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace Eav\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Collection\CollectionInterface;
// TODO: Refactor for CakePHP 5 patterns - Database\Type import updated
use Cake\Database\TypeFactory;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\FactoryLocator;

/**
 * Support class for EAV behavior.
 */
class EavToolbox
{

    /**
     * List of accepted value types.
     *
     * @var array
     */
    public static $types = [
        'biginteger',
        'binary',
        'date',
        'float',
        'decimal',
        'integer',
        'time',
        'datetime',
        'timestamp',
        'uuid',
        'string',
        'text',
        'boolean',
    ];

    /**
     * The table being managed.
     *
     * @var \Cake\ORM\Table
     */
    protected $_table = null;

    /**
     * Attributes index by bundle, and by name within each bundle.
     *
     * ```php
     * [
     *     'administrator' => [
     *         'admin-address' => [
     *             'type' => 'varchar',
     *             'searchable' => false
     *         ],
     *         'admin-phone' => [
     *             'type' => 'varchar',
     *             'searchable' => true
     *         ]
     *     ],
     *     'editor' => [
     *         'editor-last-login' => [
     *             'type' => 'datetime',
     *             'searchable' => false,
     *         ]
     *     ]
     * ]
     * ```
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table The table being handled
     */
    public function __construct(Table $table)
    {
        $this->_table = $table;
    }

    /**
     * Gets a clean column name from query expression.
     *
     * ### Example:
     *
     * ```php
     * EavToolbox::columnName('Tablename.some_column');
     * // returns "some_column"
     *
     * EavToolbox::columnName('my_column');
     * // returns "my_column"
     * ```
     *
     * @param string $column Column name from query
     * @return string
     */
    public static function columnName($column)
    {
        // TODO: Refactor for CakePHP 5 patterns - pluginSplit is deprecated
        // Quick fix: manual split instead of pluginSplit
        $parts = explode('.', (string)$column, 2);
        if (count($parts) === 2) {
            list($tableName, $fieldName) = $parts;
        } else {
            $tableName = null;
            $fieldName = $parts[0];
        }

        if (!$fieldName) {
            $fieldName = $tableName;
        }
        $fieldName = preg_replace('/\s{2,}/', ' ', $fieldName);
        list($fieldName, ) = explode(' ', trim($fieldName));

        return $fieldName;
    }

    /**
     * Checks if the provided entity has defined certain $property, regardless of
     * its value.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to check
     * @param string $property The property name
     * @return bool True if exists
     */
    public function propertyExists(EntityInterface $entity, $property)
    {
        // TODO: Refactor for CakePHP 5 patterns - visibleProperties() compatibility
        $visibleProperties = $entity->getVisible();

        return in_array($property, $visibleProperties);
    }

    /**
     * Marshalls flat data into PHP objects.
     *
     * @param mixed $value The value to convert
     * @param string $type Type identifier, `integer`, `float`, etc
     * @return mixed Converted value
     */
    public function marshal($value, $type)
    {
        // TODO: Refactor for CakePHP 5 patterns - Type::build() is deprecated
        return TypeFactory::build($type)->marshal($value);
    }

    /**
     * Gets all attributes added to this table.
     *
     * @param string|null $bundle Get attributes within given bundle, or all of them
     *  regardless of the bundle if not provided
     * @return array List of attributes indexed by name (virtual column name)
     */
    public function attributes($bundle = null)
    {
        $key = empty($bundle) ? '@all' : $bundle;
        if (isset($this->_attributes[$key])) {
            return $this->_attributes[$key];
        }

        $this->_attributes[$key] = [];
        // TODO: Refactor for CakePHP 5 patterns - table() method is deprecated
        $cacheKey = $this->_table->getTable() . '_' . $key;
        // TODO: Refactor for CakePHP 5 patterns - quick fix for cache
        $attrs = Cache::configured('eav_table_attrs') ? Cache::read($cacheKey, 'eav_table_attrs') : false;

        if (empty($attrs)) {
            $conditions = ['EavAttributes.table_alias' => $this->_table->getTable()];
            if (!empty($bundle)) {
                $conditions['EavAttributes.bundle'] = $bundle;
            }

            // TODO: Refactor for CakePHP 5 patterns - TableRegistry::get() is deprecated
            $attrs = FactoryLocator::get('Table')->get('Eav.EavAttributes')
                ->find()
                ->where($conditions)
                ->all()
                ->toArray();

            // TODO: Refactor for CakePHP 5 patterns - quick fix for cache
            if (Cache::configured('eav_table_attrs')) {
                Cache::write($cacheKey, $attrs, 'eav_table_attrs');
            }
        }

        foreach ($attrs as $attr) {
            $this->_attributes[$key][$attr->get('name')] = $attr;
        }

        return $this->attributes($bundle);
    }

    /**
     * Gets a list of attribute names.
     *
     * @param string $bundle Filter by bundle name
     * @return array
     */
    public function getAttributeNames($bundle = null)
    {
        $attributes = $this->attributes($bundle);

        return array_keys($attributes);
    }

    /**
     * Gets a list of attribute IDs.
     *
     * @param string $bundle Filter by bundle name
     * @return array
     */
    public function getAttributeIds($bundle = null)
    {
        $attributes = $this->attributes($bundle);
        $ids = [];

        foreach ($attributes as $name => $info) {
            $ids[] = $info['id'];
        }

        return $ids;
    }

    /**
     * Given a collection of entities gets the ID of all of them.
     *
     * This method iterates the given set and invokes `getEntityId()` for every
     * entity in the set.
     *
     * @param \Cake\Collection\CollectionInterface $results Set of entities
     * @return array List of entity ids suitable for EAV logic
     */
    public function extractEntityIds(CollectionInterface $results)
    {
        $entityIds = [];
        $results->each(function ($entity) use (&$entityIds) {
            if ($entity instanceof EntityInterface) {
                $entityIds[] = $this->getEntityId($entity);
            }
        });

        return $entityIds;
    }

    /**
     * Calculates entity's primary key.
     *
     * If PK is composed of multiple columns they will be merged with `:` symbol.
     * For example, consider `Users` table with composed PK <nick, email>, then for
     * certain User entity this method could return:
     *
     *     john-locke:john@the-island.com
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @return string
     */
    public function getEntityId(EntityInterface $entity)
    {
        $pk = [];
        // TODO: Refactor for CakePHP 5 patterns - primaryKey() is deprecated, use getPrimaryKey()
        $keys = $this->_table->getPrimaryKey();
        $keys = !is_array($keys) ? [$keys] : $keys;
        foreach ($keys as $key) {
            $pk[] = $entity->get($key);
        }

        return implode(':', $pk);
    }

    /**
     * Gets attribute's EAV type.
     *
     * @param string $attrName Attribute name
     * @return string Attribute's EAV type
     * @see \Eav\Model\Behavior\EavBehavior::_mapType()
     */
    public function getType($attrName)
    {
        return $this->mapType($this->attributes()[$attrName]->get('type'));
    }

    /**
     * Gets attribute's bundle.
     *
     * @param string $attrName Attribute name
     * @return string|null
     */
    public function getBundle($attrName)
    {
        return $this->attributes()[$attrName]->get('bundle');
    }

    /**
     * Whether the given attribute can be used in WHERE clauses.
     *
     * @param string $attrName Attribute name
     * @return bool
     */
    public function isSearchable($attrName)
    {
        return (bool)$this->attributes()[$attrName]->get('searchable');
    }

    /**
     * Maps schema data types to EAV's supported types.
     *
     * @param string $type A schema type. e.g. "string", "integer"
     * @return string A EAV type. Possible values are `datetime`, `binary`, `time`,
     *  `date`, `float`, `intreger`, `biginteger`, `text`, `string`, `boolean` or
     *  `uuid`
     */
    public function mapType($type)
    {
        switch ($type) {
            case 'float':
            case 'decimal':
                return 'float';
            case 'timestamp':
                return 'datetime';
            default:
                return $type;
        }
    }

    /**
     * Gets the name of the class driver used by the given $query to access the DB.
     *
     * @param \Cake\ORM\Query $query The query to inspect
     * @return string Lowercased drive name. e.g. `mysql`
     */
    public function driver(Query $query)
    {
        // TODO: Refactor for CakePHP 5 patterns - namespaceSplit is deprecated
        // Quick fix: manual namespace split
        $conn = $query->getConnection();
        $className = strtolower(get_class($conn->getDriver()));
        $parts = explode('\\', $className);
        $driver = end($parts);

        return $driver;
    }
}
