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
use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Error\FatalErrorException;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\PropertyMarshalInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\FactoryLocator;
use Cake\Utility\Hash;
use Eav\Model\Behavior\EavToolbox;
use Eav\Model\Behavior\QueryScope\QueryScopeInterface;
use Eav\Model\Behavior\QueryScope\SelectScope;
use Eav\Model\Entity\CachedColumn;
use Eav\Model\Behavior\QueryScope\WhereScope;
use \ArrayObject;

/**
 * EAV Behavior.
 *
 * Allows additional columns to be added to tables without altering its physical
 * schema.
 *
 * ### Usage:
 *
 * ```php
 * $this->addBehavior('Eav.Eav');
 * $this->addColumn('user-age', ['type' => 'integer']);
 * ```
 *
 * Using virtual attributes in WHERE clauses:
 *
 * ```php
 * $adults = $this->Users->find()
 *     ->where(['user-age >' => 18])
 *     ->all();
 * ```
 *
 * ### Using EAV Cache:
 *
 * ```php
 * $this->addBehavior('Eav.Eav', [
 *     'cache' => [
 *         'contact_info' => ['user-name', 'user-address'],
 *         'eav_all' => '*',
 *     ],
 * ]);
 * ```
 *
 * Cache all EAV values into a real column named `eav_all`:
 *
 * ```php
 * $this->addBehavior('Eav.Eav', [
 *     'cache' => 'eav_all',
 * ]);
 * ```
 *
 * @link https://github.com/quickapps/docs/blob/2.x/en/developers/field-api.rst
 */
class EavBehavior extends Behavior implements PropertyMarshalInterface
{

    /**
     * Instance of EavToolbox.
     *
     * @var \Eav\Model\Behavior\EavToolbox
     */
    protected $_toolbox = null;

    /**
     * Represents an entity that should be removed from the collection.
     *
     * @var int
     */
    const NULL_ENTITY = -1;

    /**
     * Default configuration.
     *
     * - enabled: Whether this behavior is active or not. Defaults true.
     *
     * - cache: EAV cache feature, see documentation. Defaults false.
     *
     * - hydrator: Callable function responsible of hydrate an entity with its
     *   virtual values, callable receives two arguments: the entity to hydrate and
     *   an array of virtual values, where each virtual value is an array composed
     *   of `property_name` and `value` keys.
     *
     * @var array
     */
    // TODO: Refactor for CakePHP 5 patterns
    protected array $_defaultConfig = [
        'status' => true,
        'cache' => false,
        'hydrator' => null,
        'queryScope' => [
            'Eav\\Model\\Behavior\\QueryScope\\SelectScope',
            'Eav\\Model\\Behavior\\QueryScope\\WhereScope',
            'Eav\\Model\\Behavior\\QueryScope\\OrderScope',
        ],
        'implementedMethods' => [
            'eav' => 'eav',
            'updateEavCache' => 'updateEavCache',
            'addColumn' => 'addColumn',
            'dropColumn' => 'dropColumn',
            'listColumns' => 'listColumns',
        ],
    ];

    /**
     * Query scopes objects to be applied indexed by unique ID.
     *
     * @var array
     */
    protected $_queryScopes = [];

    /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table The table this behavior is attached to
     * @param array $config Configuration array for this behavior
     */
    public function __construct(Table $table, array $config = [])
    {
        $this->_defaultConfig['hydrator'] = function (EntityInterface $entity, $values) {
            return $this->hydrateEntity($entity, $values);
        };

        $config['priority'] = -999; // EAV above anything else
        $config['cacheMap'] = false; // private config, prevent user modifications
        $this->_toolbox = new EavToolbox($table);
        parent::__construct($table, $config);

        // TODO: Refactor for CakePHP 5 patterns - quick fix for config method
        if ($this->getConfig('cache')) {
            $info = $this->getConfig('cache');
            $holders = []; // column => [list of virtual columns]

            if (is_string($info)) {
                $holders[$info] = ['*'];
            } elseif (is_array($info)) {
                foreach ($info as $column => $fields) {
                    if (is_integer($column)) {
                        $holders[$fields] = ['*'];
                    } else {
                        $holders[$column] = ($fields === '*') ? ['*'] : $fields;
                    }
                }
            }

            $this->setConfig('cacheMap', $holders);
        }
    }

    /**
     * Gets/sets EAV status.
     *
     * - TRUE: Enables EAV behavior so virtual columns WILL be fetched from database.
     * - FALSE: Disables EAV behavior so virtual columns WLL NOT be fetched from database.
     *
     * @param bool|null $status EAV status to set, or null to get current state
     * @return bool|null Current status if `$status` is set to null, otherwise null
     */
    public function eav(?bool $status = null): ?bool
    {
        if ($status === null) {
            return $this->getConfig('status');
        }

        $this->setConfig('status', $status);
        return null;
    }

    /**
     * Defines a new virtual-column, or update if already defined.
     *
     * ### Usage:
     *
     * ```php
     * $errors = $this->Users->addColumn('user-age', [
     *     'type' => 'integer',
     *     'bundle' => 'some-bundle-name',
     *     'extra' => [
     *         'option1' => 'value1'
     *     ]
     * ], true);
     *
     * if (empty($errors)) {
     *     // OK
     * } else {
     *     // ERROR
     *     debug($errors);
     * }
     * ```
     *
     * The third argument can be set to FALSE to get a boolean response:
     *
     * ```php
     * $success = $this->Users->addColumn('user-age', [
     *     'type' => 'integer',
     *     'bundle' => 'some-bundle-name',
     *     'extra' => [
     *         'option1' => 'value1'
     *     ]
     * ]);
     *
     * if ($success) {
     *     // OK
     * } else {
     *     // ERROR
     * }
     * ```
     *
     * @param string $name Column name. e.g. `user-age`
     * @param array $options Column configuration options
     * @param bool $errors If set to true will return an array list of errors
     *  instead of boolean response. Defaults to TRUE
     * @return bool|array True on success or array of error messages, depending on
     *  $error argument
     * @throws \Cake\Error\FatalErrorException When provided column name collides
     *  with existing column names. And when an invalid type is provided
     */
    public function addColumn(string $name, array $options = [], bool $errors = true): bool|array
    {
        $this->validateColumnName($name);
        $data = $this->prepareColumnData($name, $options);
        $attr = $this->findExistingAttribute($data);
        $this->validateOverwrite($attr, $data, $name);

        $attributesTable = $this->getAttributesTable();
        $attr = $this->createOrUpdateAttribute($attributesTable, $attr, $data);
        $success = $this->saveAttribute($attributesTable, $attr);

        return $errors ? (array)$attr->getErrors() : (bool)$success;
    }

    /**
     * Validates that column name doesn't conflict with existing table columns.
     *
     * @param string $name Column name to validate
     * @throws \Cake\Error\FatalErrorException When column name conflicts
     * @return void
     */
    protected function validateColumnName(string $name): void
    {
        if (in_array($name, (array)$this->_table->getSchema()->columns())) {
            throw new FatalErrorException(__d('eav', 'The column name "{0}" cannot be used as it is already defined in the table "{1}"', $name, $this->_table->getAlias()));
        }
    }

    /**
     * Prepares column data with defaults and validates type.
     *
     * @param string $name Column name
     * @param array $options Column options
     * @return array Prepared column data
     * @throws \Cake\Error\FatalErrorException When invalid type is provided
     */
    protected function prepareColumnData(string $name, array $options): array
    {
        $data = $options + [
            'type' => 'string',
            'bundle' => null,
            'searchable' => true,
            'overwrite' => false,
        ];

        $data['type'] = $this->_toolbox->mapType($data['type']);
        if (!in_array($data['type'], EavToolbox::$types)) {
            throw new FatalErrorException(__d('eav', 'The column {0}({1}) could not be created as "{2}" is not a valid type.', $name, $data['type'], $data['type']));
        }

        $data['name'] = $name;
        $data['table_alias'] = $this->_table->getTable();

        return $data;
    }

    /**
     * Finds existing attribute with given data.
     *
     * @param array $data Column data
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function findExistingAttribute(array $data): ?EntityInterface
    {
        $whereConditions = [
            'name' => $data['name'],
            'table_alias' => $data['table_alias'],
        ];

        if ($data['bundle'] === null) {
            $whereConditions['bundle IS'] = null;
        } else {
            $whereConditions['bundle'] = $data['bundle'];
        }

        return $this->getAttributesTable()
            ->find()
            ->where($whereConditions)
            ->limit(1)
            ->first();
    }

    /**
     * Validates overwrite conditions.
     *
     * @param \Cake\Datasource\EntityInterface|null $attr Existing attribute
     * @param array $data Column data
     * @param string $name Column name
     * @throws \Cake\Error\FatalErrorException When column exists and overwrite is disabled
     * @return void
     */
    protected function validateOverwrite(?EntityInterface $attr, array $data, string $name): void
    {
        if ($attr && !$data['overwrite']) {
            throw new FatalErrorException(__d('eav', 'Virtual column "{0}" already defined, use the "overwrite" option if you want to change it.', $name));
        }
    }

    /**
     * Gets the EAV attributes table instance.
     *
     * @return \Cake\ORM\Table
     */
    protected function getAttributesTable(): Table
    {
        return FactoryLocator::get('Table')->get('Eav.EavAttributes');
    }

    /**
     * Gets the EAV values table instance.
     *
     * @return \Cake\ORM\Table
     */
    protected function getValuesTable(): Table
    {
        return FactoryLocator::get('Table')->get('Eav.EavValues');
    }

    /**
     * Creates or updates an attribute entity.
     *
     * @param \Cake\ORM\Table $attributesTable Attributes table instance
     * @param \Cake\Datasource\EntityInterface|null $attr Existing attribute
     * @param array $data Column data
     * @return \Cake\Datasource\EntityInterface
     */
    protected function createOrUpdateAttribute(Table $attributesTable, ?EntityInterface $attr, array $data): EntityInterface
    {
        if ($attr && $attr->get('id') !== null) {
            return $attributesTable->patchEntity($attr, $data);
        }

        return $attributesTable->newEntity($data);
    }

    /**
     * Saves an attribute and clears cache.
     *
     * @param \Cake\ORM\Table $attributesTable Attributes table instance
     * @param \Cake\Datasource\EntityInterface $attr Attribute to save
     * @return bool Success status
     */
    protected function saveAttribute(Table $attributesTable, EntityInterface $attr): bool
    {
        $success = (bool)$attributesTable->save($attr);

        if (Cache::configured('eav_table_attrs')) {
            Cache::clear('eav_table_attrs');
        }

        return $success;
    }

    /**
     * Drops an existing column.
     *
     * @param string $name Name of the column to drop
     * @param string|null $bundle Removes the column within a particular bundle
     * @return bool True on success, false otherwise
     */
    public function dropColumn(string $name, ?string $bundle = null): bool
    {
        $whereConditions = $this->buildBundleConditions($name, $bundle);
        $attr = $this->getAttributesTable()
            ->find()
            ->where($whereConditions)
            ->limit(1)
            ->first();

        $this->clearAttributesCache();

        if (!$attr) {
            return false;
        }

        return $this->deleteAttribute($attr, $whereConditions);
    }

    /**
     * Builds WHERE conditions for bundle-aware queries.
     *
     * @param string $name Column name
     * @param string|null $bundle Bundle name
     * @return array WHERE conditions
     */
    protected function buildBundleConditions(string $name, ?string $bundle = null): array
    {
        $whereConditions = [
            'name' => $name,
            'table_alias' => $this->_table->getTable(),
        ];

        if ($bundle === null) {
            $whereConditions['bundle IS'] = null;
        } else {
            $whereConditions['bundle'] = $bundle;
        }

        return $whereConditions;
    }

    /**
     * Clears EAV attributes cache if configured.
     *
     * @return void
     */
    protected function clearAttributesCache(): void
    {
        if (Cache::configured('eav_table_attrs')) {
            Cache::clear('eav_table_attrs');
        }
    }

    /**
     * Deletes an attribute entity or falls back to deleteAll.
     *
     * @param \Cake\Datasource\EntityInterface $attr Attribute to delete
     * @param array $whereConditions Fallback conditions for deleteAll
     * @return bool Success status
     */
    protected function deleteAttribute(EntityInterface $attr, array $whereConditions): bool
    {
        $attributesTable = $this->getAttributesTable();

        if ($attr->get('id') !== null) {
            return (bool)$attributesTable->delete($attr);
        }

        $result = $attributesTable->deleteAll($whereConditions);
        return $result > 0;
    }

    /**
     * Gets a list of virtual columns attached to this table.
     *
     * @param string|null $bundle Get attributes within given bundle, or all of them
     *  regardless of the bundle if not provided
     * @return array Columns information indexed by column name
     */
    public function listColumns(?string $bundle = null): array
    {
        $columns = [];
        foreach ($this->_toolbox->attributes($bundle) as $name => $attr) {
            $columns[$name] = [
                'id' => $attr->get('id'),
                'bundle' => $attr->get('bundle'),
                'name' => $name,
                'type' => $attr->get('type'),
                'searchable' => $attr->get('searchable'),
                'extra' => $attr->get('extra'),
            ];
        }

        return $columns;
    }

    /**
     * Update EAV cache for the specified $entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to update
     * @return bool Success
     */
    public function updateEavCache(EntityInterface $entity): bool
    {
        if (!$this->getConfig('cacheMap')) {
            return false;
        }

        $attrsById = $this->getCacheAttributesById();

        if (empty($attrsById)) {
            return true;
        }

        $values = $this->fetchEntityCacheValues($entity, $attrsById);
        $toUpdate = $this->buildCacheUpdates($values);

        return $this->applyCacheUpdates($entity, $toUpdate);
    }

    /**
     * Gets attributes for cache operations indexed by ID.
     *
     * @return array Attributes indexed by ID
     */
    protected function getCacheAttributesById(): array
    {
        $attrsById = [];
        foreach ($this->_toolbox->attributes() as $attr) {
            $attrsById[$attr['id']] = $attr;
        }
        return $attrsById;
    }

    /**
     * Fetches entity values for caching.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to fetch values for
     * @param array $attrsById Attributes indexed by ID
     * @return array Values indexed by attribute name
     */
    protected function fetchEntityCacheValues(EntityInterface $entity, array $attrsById): array
    {
        $query = $this->getValuesTable()
            ->find('all')
            ->where([
                'EavValues.eav_attribute_id IN' => array_keys($attrsById),
                'EavValues.entity_id' => $this->_toolbox->getEntityId($entity),
            ])
            ->toArray();

        $values = [];
        foreach ($query as $v) {
            $attrId = $v->get('eav_attribute_id');
            $type = $attrsById[$attrId]->get('type');
            $name = $attrsById[$attrId]->get('name');
            $values[$name] = $this->_toolbox->marshal($v->get("value_{$type}"), $type);
        }

        return $values;
    }

    /**
     * Builds cache column updates.
     *
     * @param array $values EAV values indexed by name
     * @return array Cache updates indexed by column name
     */
    protected function buildCacheUpdates(array $values): array
    {
        $toUpdate = [];

        foreach ((array)$this->getConfig('cacheMap') as $column => $fields) {
            $cache = $this->buildCacheColumnData($values, $fields);
            $toUpdate[$column] = $this->serializeCacheData($cache);
        }

        return $toUpdate;
    }

    /**
     * Builds cache data for a single column.
     *
     * @param array $values All EAV values
     * @param array $fields Fields to include in cache
     * @return array Cache data
     */
    protected function buildCacheColumnData(array $values, array $fields): array
    {
        if (in_array('*', $fields)) {
            return $values;
        }

        $cache = [];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $cache[$field] = $values[$field];
            }
        }

        return $cache;
    }

    /**
     * Serializes cache data using CachedColumn entity.
     *
     * @param array $cache Cache data to serialize
     * @return string Serialized cache data
     */
    protected function serializeCacheData(array $cache): string
    {
        if (!class_exists('Eav\Model\Entity\CachedColumn')) {
            require_once dirname(__DIR__) . '/Entity/CachedColumn.php';
        }
        return (string)serialize(new \Eav\Model\Entity\CachedColumn($cache));
    }

    /**
     * Applies cache updates to the entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to update
     * @param array $toUpdate Cache updates
     * @return bool Success status
     */
    protected function applyCacheUpdates(EntityInterface $entity, array $toUpdate): bool
    {
        if (empty($toUpdate)) {
            return true;
        }

        if (!$this->validateCacheColumns($toUpdate)) {
            return false;
        }

        $conditions = $this->buildPrimaryKeyConditions($entity);

        if (empty($conditions)) {
            return false;
        }

        return (bool)$this->_table->updateAll($toUpdate, $conditions);
    }

    /**
     * Validates that cache columns exist in table schema.
     *
     * @param array $toUpdate Cache updates
     * @return bool True if all columns exist
     */
    protected function validateCacheColumns(array $toUpdate): bool
    {
        $tableColumns = $this->_table->getSchema()->columns();
        foreach ($toUpdate as $column => $value) {
            if (!in_array($column, $tableColumns)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Builds primary key conditions for entity updates.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @return array Primary key conditions
     */
    protected function buildPrimaryKeyConditions(EntityInterface $entity): array
    {
        $conditions = [];
        $keys = $this->_table->getPrimaryKey();
        $keys = !is_array($keys) ? [$keys] : $keys;

        foreach ($keys as $key) {
            $conditions[$key] = $entity->get($key);
        }

        return $conditions;
    }

    /**
     * Attaches virtual properties to entities.
     *
     * This method is also responsible of looking for virtual columns in SELECT and
     * WHERE clauses (if applicable) and properly scope the Query object. Query
     * scoping is performed by the `_scopeQuery()` method.
     *
     * EAV can be enabled or disabled on the fly using `eav` finder option, or
     * `eav()` method. When mixing, `eav` option has the highest priority:
     *
     * ```php
     * $this->Articles->eav(false);
     * $articlesNoVirtual = $this->Articles->find('all');
     * $articlesWithVirtual = $this->Articles->find('all', ['eav' => true]);
     * ```
     *
     * @param \Cake\Event\Event $event The beforeFind event that was triggered
     * @param \Cake\ORM\Query $query The original query to modify
     * @param \ArrayObject $options Additional options given as an array
     * @param bool $primary Whether this find is a primary query or not
     * @return void
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, bool $primary): void
    {
        $status = $options['eav'] ?? $this->getConfig('status');

        if (!$status) {
            return;
        }

        $options['bundle'] = $options['bundle'] ?? null;
        $this->_initScopes();

        $selectScope = $this->_queryScopes['Eav\\Model\\Behavior\\QueryScope\\SelectScope'] ?? null;
        if (!$selectScope) {
            $event->setResult($query);
            return;
        }

        $selectedVirtual = $selectScope->getVirtualColumns($query, $options['bundle']);
        $args = compact('options', 'primary', 'selectedVirtual');
        $query = $this->_scopeQuery($query, $options['bundle']);

        $modifiedQuery = $query->formatResults(function ($results) use ($args) {
            return $this->_hydrateEntities($results, $args);
        }, Query::PREPEND);

        $event->setResult($modifiedQuery);
    }

    /**
     * Attach EAV attributes for every entity in the provided result-set.
     *
     * This method iterates over each retrieved entity and invokes the
     * `hydrateEntity()` method. This last should return the altered entity object
     * with all its virtual properties, however if this method returns NULL the
     * entity will be removed from the resulting collection.
     *
     * @param \Cake\Collection\CollectionInterface $entities Set of entities to be
     *  processed
     * @param array $args Contains three keys: "options" and "primary" given to the
     *  originating beforeFind(), and "selectedVirtual", a list of virtual columns
     *  selected in the originating find query
     * @return \Cake\Collection\CollectionInterface New set with altered entities
     */
    protected function _hydrateEntities(CollectionInterface $entities, array $args): CollectionInterface
    {
        $values = $this->_prepareSetValues($entities, $args);
        $hydrator = $this->getConfig('hydrator');

        return $entities->map(function ($entity) use ($values, $hydrator) {
            if (!($entity instanceof EntityInterface)) {
                return $entity;
            }

            $entity = $this->_prepareCachedColumns($entity);
            $entityId = $this->_toolbox->getEntityId($entity);
            $entityValues = $values[$entityId] ?? [];
            $entity = $hydrator($entity, $entityValues);

            return $entity ?? self::NULL_ENTITY;
        })
        ->filter(function ($entity) {
            return $entity !== self::NULL_ENTITY;
        });
    }

    /**
     * Hydrates a single entity and returns it.
     *
     * Returning NULL indicates the entity should be removed from the resulting
     * collection.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to hydrate
     * @param array $values Holds stored virtual values for this particular entity
     * @return \Cake\Datasource\EntityInterface|null
     */
    public function hydrateEntity(EntityInterface $entity, array $values): ?EntityInterface
    {
        foreach ($values as $value) {
            if (!$this->_toolbox->propertyExists($entity, $value['property_name'])) {
                $entity->set($value['property_name'], $value['value']);
                $entity->setDirty($value['property_name'], false);
            }
        }

        $this->normalizeCacheColumns($entity);

        return $entity;
    }

    /**
     * Normalizes cache columns to proper Entity type.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to normalize
     * @return void
     */
    protected function normalizeCacheColumns(EntityInterface $entity): void
    {
        if (!$this->getConfig('cacheMap')) {
            return;
        }

        foreach ($this->getConfig('cacheMap') as $column => $fields) {
            if ($this->_toolbox->propertyExists($entity, $column) && !($entity->get($column) instanceof Entity)) {
                $entity->set($column, new Entity());
            }
        }
    }

    /**
     * Retrieves all virtual values of all the entities within the given result-set.
     *
     * @param \Cake\Collection\CollectionInterface $entities Set of entities
     * @param array $args Contains two keys: "options" and "primary" given to the
     *  originating beforeFind(), and "selectedVirtual" a list of virtual columns
     *  selected in the originating find query
     * @return array Virtual values indexed by entity ID
     */
    protected function _prepareSetValues(CollectionInterface $entities, array $args): array
    {
        $entityIds = $this->_toolbox->extractEntityIds($entities);

        if (empty($entityIds)) {
            return [];
        }

        $attrsById = $this->getValidAttributesById($args);

        if (empty($attrsById)) {
            return [];
        }

        $fetchedRawValues = $this->fetchRawValues($entityIds, $attrsById, $args['selectedVirtual']);
        return $this->buildFetchedValues($entityIds, $attrsById, $fetchedRawValues);
    }

    /**
     * Gets valid attributes filtered by selected virtual columns.
     *
     * @param array $args Query arguments containing bundle and selectedVirtual
     * @return array Attributes indexed by ID
     */
    protected function getValidAttributesById(array $args): array
    {
        $selectedVirtual = $args['selectedVirtual'];
        $bundle = $args['options']['bundle'];
        $validColumns = array_values($selectedVirtual);
        $validNames = array_intersect($this->_toolbox->getAttributeNames($bundle), $validColumns);
        $attrsById = [];

        foreach ($this->_toolbox->attributes($bundle) as $name => $attr) {
            if (in_array($name, $validNames)) {
                $attrsById[$attr['id']] = $attr;
            }
        }

        return $attrsById;
    }

    /**
     * Fetches raw EAV values from database.
     *
     * @param array $entityIds Entity IDs to fetch values for
     * @param array $attrsById Attributes indexed by ID
     * @param array $selectedVirtual Selected virtual columns
     * @return array Raw values grouped by entity ID
     */
    protected function fetchRawValues(array $entityIds, array $attrsById, array $selectedVirtual): array
    {
        return $this->getValuesTable()
            ->find('all')
            ->where([
                'EavValues.eav_attribute_id IN' => array_keys($attrsById),
                'EavValues.entity_id IN' => $entityIds,
            ])
            ->all()
            ->map(function ($value) use ($attrsById, $selectedVirtual) {
                return $this->mapValueRecord($value, $attrsById, $selectedVirtual);
            })
            ->groupBy('entity_id')
            ->map(function ($values) {
                return (new Collection($values))->indexBy('attribute_id')->toArray();
            })
            ->toArray();
    }

    /**
     * Maps a single value record to standardized format.
     *
     * @param \Cake\Datasource\EntityInterface $value Value record
     * @param array $attrsById Attributes indexed by ID
     * @param array $selectedVirtual Selected virtual columns
     * @return array Mapped value record
     */
    protected function mapValueRecord(EntityInterface $value, array $attrsById, array $selectedVirtual): array
    {
        $attrId = $value->get('eav_attribute_id');
        $attrName = $attrsById[$attrId]->get('name');
        $attrType = $attrsById[$attrId]->get('type');
        $alias = array_search($attrName, $selectedVirtual);

        return [
            'attribute_id' => $attrId,
            'entity_id' => $value->get('entity_id'),
            'property_name' => is_string($alias) ? $alias : $attrName,
            'property_name_real' => $attrName,
            'aliased' => is_string($alias),
            'value' => $this->_toolbox->marshal($value->get("value_{$attrType}"), $attrType),
        ];
    }

    /**
     * Builds the final fetched values array with defaults for missing values.
     *
     * @param array $entityIds Entity IDs
     * @param array $attrsById Attributes indexed by ID
     * @param array $fetchedRawValues Raw values from database
     * @return array Complete fetched values array
     */
    protected function buildFetchedValues(array $entityIds, array $attrsById, array $fetchedRawValues): array
    {
        $fetchedValues = [];

        foreach ($entityIds as $entityId) {
            $fetchedValues[$entityId] = [];
            $values = $fetchedRawValues[$entityId] ?? [];

            foreach ($attrsById as $attrId => $attributeInfo) {
                if (isset($values[$attrId])) {
                    $fetchedValues[$entityId][] = $values[$attrId];
                } else {
                    $fetchedValues[$entityId][] = $this->createDefaultValue($attrId, $entityId, $attributeInfo);
                }
            }
        }

        return $fetchedValues;
    }

    /**
     * Creates a default value record for missing attributes.
     *
     * @param int $attrId Attribute ID
     * @param mixed $entityId Entity ID
     * @param \Cake\Datasource\EntityInterface $attributeInfo Attribute info
     * @return array Default value record
     */
    protected function createDefaultValue(int $attrId, $entityId, EntityInterface $attributeInfo): array
    {
        return [
            'attribute_id' => $attrId,
            'entity_id' => $entityId,
            'property_name' => $attributeInfo->get('name'),
            'property_name_real' => $attributeInfo->get('name'),
            'aliased' => false,
            'value' => null,
        ];
    }

    /**
     * Triggered before data is converted into entities.
     *
     * Converts incoming POST data to its corresponding types.
     *
     * @param \Cake\Event\Event $event The event that was triggered
     * @param \ArrayObject $data The POST data to be merged with entity
     * @param \ArrayObject $options The options passed to the marshaller
     * @return void
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options): void
    {
        $bundle = !empty($options['bundle']) ? $options['bundle'] : null;
        $attrs = array_keys($this->_toolbox->attributes($bundle));

        foreach ($data as $property => $value) {
            if (!in_array($property, $attrs)) {
                continue;
            }

            $dataType = $this->_toolbox->getType($property);
            $marshaledValue = $this->_toolbox->marshal($value, $dataType);
            $data[$property] = $marshaledValue;
        }
    }

    /**
     * Ensures that virtual properties are included in the marshalling process.
     *
     * @param \Cake\ORM\Marshaller $marshaller The marshaller of the table the behavior is attached to.
     * @param array $map The property map being built.
     * @param array $options The options array used in the marshalling call.
     * @return array A map of `[property => callable]` of additional properties to marshal.
     */
    public function buildMarshalMap(\Cake\ORM\Marshaller $marshaller, array $map, array $options): array
    {
        $bundle = $options['bundle'] ?? null;
        $attrs = $this->_toolbox->attributes($bundle);
        $marshalMap = [];

        foreach ($attrs as $name => $info) {
            $marshalMap[$name] = function ($value, $entity) use ($info) {
                return $this->_toolbox->marshal($value, $info['type']);
            };
        }

        return $marshalMap;
    }

    /**
     * Save virtual values after an entity's real values were saved.
     *
     * @param \Cake\Event\Event $event The event that was triggered
     * @param \Cake\Datasource\EntityInterface $entity The entity that was saved
     * @param \ArrayObject $options Additional options given as an array
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        $valuesTable = $this->getValuesTable();
        $result = $valuesTable
            ->getConnection()
            ->transactional(function () use ($valuesTable, $entity) {
                return $this->saveEntityEavValues($valuesTable, $entity);
            });

        $event->setResult($result);
    }

    /**
     * Saves EAV values for an entity within a transaction.
     *
     * @param \Cake\ORM\Table $valuesTable EAV values table
     * @param \Cake\Datasource\EntityInterface $entity Entity to save values for
     * @return bool Success status
     */
    protected function saveEntityEavValues(Table $valuesTable, EntityInterface $entity): bool
    {
        $attrsById = $this->getEntityAttributesById($entity);

        if (empty($attrsById)) {
            return true;
        }

        $existingValues = $this->getExistingEavValues($valuesTable, $entity, $attrsById);
        $updatedAttrs = $this->updateExistingValues($valuesTable, $entity, $existingValues, $attrsById);
        $this->createNewValues($valuesTable, $entity, $attrsById, $updatedAttrs);

        if ($this->getConfig('cacheMap')) {
            $this->updateEavCache($entity);
        }

        return true;
    }

    /**
     * Gets attributes that exist as properties on the entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to check
     * @return array Attributes indexed by ID
     */
    protected function getEntityAttributesById(EntityInterface $entity): array
    {
        $attrsById = [];

        foreach ($this->_toolbox->attributes() as $name => $attr) {
            if ($this->_toolbox->propertyExists($entity, $name)) {
                $attrsById[$attr->get('id')] = $attr;
            }
        }

        return $attrsById;
    }

    /**
     * Gets existing EAV values for the entity with row locking.
     *
     * @param \Cake\ORM\Table $valuesTable Values table
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param array $attrsById Attributes by ID
     * @return \Cake\ORM\Query Values query with locking
     */
    protected function getExistingEavValues(Table $valuesTable, EntityInterface $entity, array $attrsById): Query
    {
        $values = $valuesTable
            ->find()
            ->where([
                'eav_attribute_id IN' => array_keys($attrsById),
                'entity_id' => $this->_toolbox->getEntityId($entity),
            ]);

        $driver = $this->_toolbox->driver($values);
        if (in_array($driver, ['mysql', 'postgres'])) {
            $values->epilog('FOR UPDATE');
        }

        return $values;
    }

    /**
     * Updates existing EAV values.
     *
     * @param \Cake\ORM\Table $valuesTable Values table
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param \Cake\ORM\Query $existingValues Existing values query
     * @param array $attrsById Attributes by ID
     * @return array Array of updated attribute IDs
     */
    protected function updateExistingValues(Table $valuesTable, EntityInterface $entity, Query $existingValues, array $attrsById): array
    {
        $updatedAttrs = [];

        foreach ($existingValues as $value) {
            $attrId = $value->get('eav_attribute_id');
            $updatedAttrs[] = $attrId;
            $info = $attrsById[$attrId];
            $type = $this->_toolbox->getType($info->get('name'));
            $propertyValue = $entity->get($info->get('name'));

            $marshaledValue = $this->_toolbox->marshal($propertyValue, $type);
            $value->set("value_{$type}", $marshaledValue);
            $entity->set($info->get('name'), $marshaledValue);
            $valuesTable->save($value);
        }

        return $updatedAttrs;
    }

    /**
     * Creates new EAV values for attributes not yet stored.
     *
     * @param \Cake\ORM\Table $valuesTable Values table
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param array $attrsById Attributes by ID
     * @param array $updatedAttrs Already updated attribute IDs
     * @return void
     */
    protected function createNewValues(Table $valuesTable, EntityInterface $entity, array $attrsById, array $updatedAttrs): void
    {
        foreach ($this->_toolbox->attributes() as $name => $attr) {
            if (!$this->_toolbox->propertyExists($entity, $name)) {
                continue;
            }

            if (!in_array($attr->get('id'), $updatedAttrs)) {
                $type = $this->_toolbox->getType($name);
                $value = $valuesTable->newEntity([
                    'eav_attribute_id' => $attr->get('id'),
                    'entity_id' => $this->_toolbox->getEntityId($entity),
                ]);

                $propertyValue = $entity->get($name);
                $marshaledValue = $this->_toolbox->marshal($propertyValue, $type);
                $value->set("value_{$type}", $marshaledValue);
                $entity->set($name, $marshaledValue);
                $valuesTable->save($value);
            }
        }
    }

    /**
     * Before an entity is deleted from database. Check if operation is atomic.
     *
     * @param \Cake\Event\Event $event The event that was triggered
     * @param \Cake\Datasource\EntityInterface $entity The entity being deleted
     * @param \ArrayObject $options Additional options given as an array
     * @throws \Cake\Error\FatalErrorException When using this behavior in non-atomic mode
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$options['atomic']) {
            throw new FatalErrorException(__d('eav', 'Entities in fieldable tables can only be deleted using transactions. Set [atomic = true]'));
        }
    }

    /**
     * After an entity was removed from database. Here is when EAV values are
     * removed from DB.
     *
     * @param \Cake\Event\Event $event The event that was triggered
     * @param \Cake\Datasource\EntityInterface $entity The entity that was deleted
     * @param \ArrayObject $options Additional options given as an array
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        $this->deleteEntityEavValues($entity);
    }

    /**
     * Deletes all EAV values associated with an entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to delete values for
     * @return void
     */
    protected function deleteEntityEavValues(EntityInterface $entity): void
    {
        $valuesToDelete = $this->getValuesTable()
            ->find()
            ->join([
                'EavAttribute' => [
                    'table' => 'eav_attributes',
                    'type' => 'INNER',
                    'conditions' => ['EavValues.eav_attribute_id = EavAttribute.id']
                ]
            ])
            ->where([
                'EavAttribute.table_alias' => $this->_table->getTable(),
                'EavValues.entity_id' => $this->_toolbox->getEntityId($entity),
            ]);

        foreach ($valuesToDelete as $value) {
            $this->getValuesTable()->delete($value);
        }
    }

    /**
     * Prepares entity's cache-columns (those defined using `cache` option).
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to prepare
     * @return \Cake\Datasource\EntityInterface Modified entity
     */
    protected function _prepareCachedColumns(EntityInterface $entity): EntityInterface
    {
        if (!$this->getConfig('cacheMap')) {
            return $entity;
        }

        foreach ((array)$this->getConfig('cacheMap') as $column => $fields) {
            if (in_array($column, $entity->getVisible())) {
                $this->processCachedColumn($entity, $column);
            }
        }

        return $entity;
    }

    /**
     * Processes a single cached column for an entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to process
     * @param string $column Column name to process
     * @return void
     */
    protected function processCachedColumn(EntityInterface $entity, string $column): void
    {
        $string = $entity->get($column);

        if ($string == serialize(false) || @unserialize($string) !== false) {
            $entity->set($column, unserialize($string));
        } else {
            $entity->set($column, new CachedColumn());
        }
    }

    /**
     * Look for virtual columns in some query's clauses.
     *
     * @param \Cake\ORM\Query $query The query to scope
     * @param string|null $bundle Consider attributes only for a specific bundle
     * @return \Cake\ORM\Query The modified query object
     */
    protected function _scopeQuery(Query $query, ?string $bundle = null): Query
    {
        $this->_initScopes();

        foreach ($this->_queryScopes as $scope) {
            if ($scope instanceof QueryScopeInterface) {
                $query = $scope->scope($query, $bundle);
            }
        }

        return $query;
    }

    /**
     * Initializes the scope objects
     *
     * @return void
     */
    protected function _initScopes(): void
    {
        foreach ((array)$this->getConfig('queryScope') as $className) {
            if (!empty($this->_queryScopes[$className])) {
                continue;
            }

            if (class_exists($className)) {
                $instance = new $className($this->_table);
                if ($instance instanceof QueryScopeInterface) {
                    $this->_queryScopes[$className] = $instance;
                }
            }
        }
    }
}
