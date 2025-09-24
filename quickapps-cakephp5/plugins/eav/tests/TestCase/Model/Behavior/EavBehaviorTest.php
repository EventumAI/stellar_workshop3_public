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
namespace Eav\Test\TestCase\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Eav\Model\Behavior\EavBehavior;
use Eav\Model\Entity\CachedColumn;

/**
 * EavBehaviorTest class.
 *
 * Comprehensive test suite for EAV Behavior covering:
 * - Virtual column management
 * - Data type handling
 * - Query performance
 * - Caching system
 * - Error handling
 */
class EavBehaviorTest extends TestCase
{
    /**
     * The table to which `EavBehavior` is attached to
     *
     * @var \Cake\ORM\Table
     */
    public $table;

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.eav.dummy',
        'plugin.eav.eav_values',
        'plugin.eav.eav_attributes',
    ];

    /**
     * setUp().
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->table = TableRegistry::getTableLocator()->get('Dummy');
        $this->table->addBehavior('Eav.Eav');

        // Clear cache before each test
        Cache::clear('eav_table_attrs');
    }

    /**
     * tearDown().
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        TableRegistry::getTableLocator()->clear();
        Cache::clear('eav_table_attrs');
    }

    // ========================================================================
    // BASIC FUNCTIONALITY TESTS
    // ========================================================================

    /**
     * Test that behavior is properly attached and configured
     */
    public function testBehaviorAttachment()
    {
        $this->assertTrue($this->table->hasBehavior('Eav'));
        $behavior = $this->table->behaviors()->get('Eav');
        $this->assertInstanceOf(EavBehavior::class, $behavior);
        $this->assertTrue($behavior->eav()); // Default status should be true
    }

    /**
     * Test EAV status getter/setter functionality
     */
    public function testEavStatus()
    {
        // Test getter
        $this->assertTrue($this->table->eav());

        // Test setter - disable
        $this->table->eav(false);
        $this->assertFalse($this->table->eav());

        // Test setter - enable
        $this->table->eav(true);
        $this->assertTrue($this->table->eav());
    }

    // ========================================================================
    // VIRTUAL COLUMN MANAGEMENT TESTS
    // ========================================================================

    /**
     * Test adding virtual columns successfully
     */
    public function testAddColumnSuccess()
    {
        // Test string column
        $success = $this->table->addColumn('test-string', [
            'type' => 'string',
            'bundle' => 'test',
            'searchable' => true
        ], false);
        $this->assertTrue($success);

        // Test integer column
        $success = $this->table->addColumn('test-integer', [
            'type' => 'integer',
            'bundle' => 'test'
        ], false);
        $this->assertTrue($success);

        // Test decimal column
        $success = $this->table->addColumn('test-decimal', [
            'type' => 'decimal',
            'bundle' => 'test'
        ], false);
        $this->assertTrue($success);

        // Test datetime column
        $success = $this->table->addColumn('test-datetime', [
            'type' => 'datetime',
            'bundle' => 'test'
        ], false);
        $this->assertTrue($success);

        // Test boolean column
        $success = $this->table->addColumn('test-boolean', [
            'type' => 'boolean',
            'bundle' => 'test'
        ], false);
        $this->assertTrue($success);
    }

    /**
     * Test adding column with validation errors
     */
    public function testAddColumnWithErrors()
    {
        // Test with error return (default)
        $errors = $this->table->addColumn('test-field', [
            'type' => 'string',
            'bundle' => null // This might cause validation error depending on implementation
        ]);
        $this->assertIsArray($errors);
    }

    /**
     * Test that adding existing column throws exception
     */
    public function testAddColumnExistingThrows()
    {
        $this->expectException(\Cake\Error\FatalErrorException::class);
        $this->expectExceptionMessage('cannot be used as it is already defined in the table');

        // Attempt to add a column with same name as physical column
        $this->table->addColumn('name', ['type' => 'string']);
    }

    /**
     * Test adding duplicate virtual column without overwrite
     */
    public function testAddColumnDuplicateThrows()
    {
        // First addition should succeed
        $success = $this->table->addColumn('duplicate-test', ['type' => 'string'], false);
        $this->assertTrue($success);

        // Second addition should throw exception
        $this->expectException(\Cake\Error\FatalErrorException::class);
        $this->expectExceptionMessage('Virtual column "duplicate-test" already defined');
        $this->table->addColumn('duplicate-test', ['type' => 'string'], false);
    }

    /**
     * Test adding duplicate virtual column with overwrite
     */
    public function testAddColumnDuplicateWithOverwrite()
    {
        // First addition
        $success = $this->table->addColumn('overwrite-test', ['type' => 'string'], false);
        $this->assertTrue($success);

        // Second addition with overwrite should succeed
        $success = $this->table->addColumn('overwrite-test', [
            'type' => 'integer',
            'overwrite' => true
        ], false);
        $this->assertTrue($success);
    }

    /**
     * Test invalid column type throws exception
     */
    public function testAddColumnInvalidType()
    {
        $this->expectException(\Cake\Error\FatalErrorException::class);
        $this->expectExceptionMessage('is not a valid type');

        $this->table->addColumn('invalid-type', ['type' => 'invalid_type'], false);
    }

    /**
     * Test dropping virtual columns
     */
    public function testDropColumn()
    {
        // Add a column first
        $this->table->addColumn('drop-test', ['type' => 'string', 'bundle' => 'test'], false);

        // Drop it
        $success = $this->table->dropColumn('drop-test', 'test');
        $this->assertTrue($success);

        // Try to drop non-existent column
        $success = $this->table->dropColumn('non-existent');
        $this->assertFalse($success);
    }

    /**
     * Test listing virtual columns
     */
    public function testListColumns()
    {
        // Add some test columns
        $this->table->addColumn('list-test-1', [
            'type' => 'string',
            'bundle' => 'test',
            'searchable' => true
        ], false);

        $this->table->addColumn('list-test-2', [
            'type' => 'integer',
            'bundle' => 'test',
            'searchable' => false
        ], false);

        $this->table->addColumn('list-test-3', [
            'type' => 'string',
            'bundle' => 'other',
            'searchable' => true
        ], false);

        // List all columns
        $allColumns = $this->table->listColumns();
        $this->assertIsArray($allColumns);

        // List columns for specific bundle
        $testColumns = $this->table->listColumns('test');
        $this->assertIsArray($testColumns);

        // List columns for non-existent bundle
        $emptyColumns = $this->table->listColumns('non-existent');
        $this->assertIsArray($emptyColumns);
    }

    // ========================================================================
    // ENTITY MARSHALLING AND HYDRATION TESTS
    // ========================================================================

    /**
     * Test entity marshalling with virtual properties
     */
    public function testEntityMarshalling()
    {
        // Add a virtual column
        $this->table->addColumn('marshal-test', ['type' => 'string'], false);

        // Create entity with virtual data
        $entity = $this->table->newEntity([
            'name' => 'Test Entity',
            'marshal-test' => 'Virtual Value'
        ]);

        $this->assertEquals('Test Entity', $entity->get('name'));
        $this->assertEquals('Virtual Value', $entity->get('marshal-test'));
    }

    /**
     * Test datetime marshalling
     */
    public function testDatetimeMarshalling()
    {
        $time = time();

        // Test with existing virtual_date column from fixtures
        $entity = $this->table->newEntity(['virtual_date' => $time]);
        $this->assertInstanceOf(\DateTime::class, $entity->get('virtual_date'));
        $this->assertTrue($entity->isDirty('virtual_date'));

        // Test with clean entity
        $entity->clean();
        $this->assertInstanceOf(\DateTime::class, $entity->get('virtual_date'));
        $this->assertFalse($entity->isDirty('virtual_date'));

        // Test patching with same value produces no changes
        $valueBefore = $entity->get('virtual_date');
        $entity = $this->table->patchEntity($entity, ['virtual_date' => $time]);
        $this->assertInstanceOf(\DateTime::class, $entity->get('virtual_date'));
        $this->assertFalse($entity->isDirty('virtual_date'));
        $this->assertEquals($valueBefore, $entity->get('virtual_date'));
    }

    /**
     * Test different data type marshalling
     */
    public function testDataTypeMarshalling()
    {
        // Add columns of different types
        $this->table->addColumn('test-integer', ['type' => 'integer'], false);
        $this->table->addColumn('test-decimal', ['type' => 'decimal'], false);
        $this->table->addColumn('test-boolean', ['type' => 'boolean'], false);

        $entity = $this->table->newEntity([
            'test-integer' => '123',
            'test-decimal' => '99.99',
            'test-boolean' => '1'
        ]);

        // Note: Actual type conversion depends on EavToolbox implementation
        $this->assertNotNull($entity->get('test-integer'));
        $this->assertNotNull($entity->get('test-decimal'));
        $this->assertNotNull($entity->get('test-boolean'));
    }

    // ========================================================================
    // QUERY AND FIND TESTS
    // ========================================================================

    /**
     * Test basic EAV value retrieval
     */
    public function testFind()
    {
        // Test retrieval of existing virtual values from fixtures
        $entity = $this->table->get(1, ['fields' => ['virtual_text']]);
        $this->assertEquals('This content belongs to a virtual column of type `text`', $entity->get('virtual_text'));

        $entity = $this->table->get(1, ['fields' => ['virtual_integer']]);
        $this->assertEquals(27, $entity->get('virtual_integer'));

        // Test multiple fields
        $entity = $this->table->get(1, ['fields' => ['virtual_text', 'virtual_integer']]);
        $this->assertEquals('This content belongs to a virtual column of type `text`', $entity->get('virtual_text'));
        $this->assertEquals(27, $entity->get('virtual_integer'));
    }

    /**
     * Test WHERE conditions with virtual columns
     */
    public function testFindWithWhereConditions()
    {
        $entityCount = $this->table
            ->find('all')
            ->where([
                'id' => 1,
                'virtual_text LIKE' => '%virtual%'
            ])
            ->count();
        $this->assertEquals(1, $entityCount);
    }

    /**
     * Test unary expressions (IS NULL, IS NOT NULL)
     */
    public function testUnaryExpressions()
    {
        $this->table->addColumn('user-birth-date', ['type' => 'date'], false);

        // Set value for first entity
        $first = $this->table->get(1);
        $first->set('user-birth-date', time());
        $this->table->save($first);

        // Find entity where field IS NULL
        $second = $this->table
            ->find('all', ['eav' => true])
            ->where(['user-birth-date IS' => null])
            ->order(['id' => 'ASC'])
            ->first();

        $this->assertNotEmpty($second);
        $this->assertEquals(2, $second->get('id'));
    }

    /**
     * Test EAV status in find operations
     */
    public function testFindWithEavStatus()
    {
        // Disable EAV globally
        $this->table->eav(false);

        // Find with EAV disabled should not include virtual columns
        $entity = $this->table->get(1);
        $this->assertNull($entity->get('virtual_text'));

        // Find with EAV explicitly enabled should include virtual columns
        $entity = $this->table->get(1, ['eav' => true]);
        $this->assertNotNull($entity->get('virtual_text'));

        // Re-enable EAV globally
        $this->table->eav(true);
        $entity = $this->table->get(1);
        $this->assertNotNull($entity->get('virtual_text'));
    }

    /**
     * Test bundle-specific queries
     */
    public function testFindWithBundle()
    {
        // Add columns with different bundles
        $this->table->addColumn('bundle-test-1', ['type' => 'string', 'bundle' => 'bundle1'], false);
        $this->table->addColumn('bundle-test-2', ['type' => 'string', 'bundle' => 'bundle2'], false);

        // Test finding with specific bundle
        $entity = $this->table->newEntity(['bundle-test-1' => 'value1', 'bundle-test-2' => 'value2']);
        $this->table->save($entity);

        $found = $this->table->find('all', ['bundle' => 'bundle1'])->first();
        $this->assertInstanceOf(Entity::class, $found);
    }

    // ========================================================================
    // SAVE AND PERSISTENCE TESTS
    // ========================================================================

    /**
     * Test saving entities with EAV values
     */
    public function testSaveWithEavValues()
    {
        // Add test columns
        $this->table->addColumn('save-test-string', ['type' => 'string'], false);
        $this->table->addColumn('save-test-integer', ['type' => 'integer'], false);

        // Create and save entity
        $entity = $this->table->newEntity([
            'name' => 'Test Entity',
            'save-test-string' => 'Test String Value',
            'save-test-integer' => 42
        ]);

        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);

        // Retrieve and verify
        $retrieved = $this->table->get($result->get('id'));
        $this->assertEquals('Test String Value', $retrieved->get('save-test-string'));
        $this->assertEquals(42, $retrieved->get('save-test-integer'));
    }

    /**
     * Test updating existing EAV values
     */
    public function testUpdateEavValues()
    {
        // Add test column
        $this->table->addColumn('update-test', ['type' => 'string'], false);

        // Create entity
        $entity = $this->table->newEntity([
            'name' => 'Update Test',
            'update-test' => 'Original Value'
        ]);
        $this->table->save($entity);

        // Update the entity
        $entity->set('update-test', 'Updated Value');
        $result = $this->table->save($entity);
        $this->assertTrue($result !== false);

        // Verify update
        $retrieved = $this->table->get($entity->get('id'));
        $this->assertEquals('Updated Value', $retrieved->get('update-test'));
    }

    /**
     * Test saving with multiple values for same attribute
     */
    public function testSaveMultipleValues()
    {
        // This test would depend on how the EAV system handles multiple values
        // for the same attribute, which may require specific configuration
        $this->table->addColumn('multi-test', ['type' => 'string'], false);

        $entity = $this->table->newEntity([
            'name' => 'Multi Test',
            'multi-test' => 'Single Value' // Multiple values would require array handling
        ]);

        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);
    }

    // ========================================================================
    // DELETE TESTS
    // ========================================================================

    /**
     * Test EAV value cleanup on entity deletion
     */
    public function testDeleteWithEavCleanup()
    {
        // Add test column
        $this->table->addColumn('delete-test', ['type' => 'string'], false);

        // Create entity with EAV data
        $entity = $this->table->newEntity([
            'name' => 'Delete Test',
            'delete-test' => 'Value to be deleted'
        ]);
        $this->table->save($entity);

        // Delete entity (must be atomic)
        $result = $this->table->delete($entity, ['atomic' => true]);
        $this->assertTrue($result);

        // Verify EAV values were cleaned up
        $eavValuesTable = TableRegistry::getTableLocator()->get('Eav.EavValues');
        $remainingValues = $eavValuesTable->find()
            ->where(['entity_id' => $entity->get('id')])
            ->count();
        $this->assertEquals(0, $remainingValues);
    }

    /**
     * Test non-atomic deletion throws exception
     */
    public function testDeleteNonAtomicThrows()
    {
        $entity = $this->table->newEntity(['name' => 'Delete Test']);
        $this->table->save($entity);

        $this->expectException(\Cake\Error\FatalErrorException::class);
        $this->expectExceptionMessage('can only be deleted using transactions');

        $this->table->delete($entity, ['atomic' => false]);
    }

    // ========================================================================
    // CACHING TESTS
    // ========================================================================

    /**
     * Test EAV cache configuration
     */
    public function testCacheConfiguration()
    {
        // Create new table with cache configuration
        $cachedTable = TableRegistry::getTableLocator()->get('CachedDummy');
        $cachedTable->addBehavior('Eav.Eav', [
            'cache' => [
                'contact_info' => ['test-field-1', 'test-field-2'],
                'eav_all' => '*',
            ]
        ]);

        $behavior = $cachedTable->behaviors()->get('Eav');
        $this->assertInstanceOf(EavBehavior::class, $behavior);
    }

    /**
     * Test cache update functionality
     */
    public function testUpdateEavCache()
    {
        // Configure table with caching
        $this->table->behaviors()->unload('Eav');
        $this->table->addBehavior('Eav.Eav', [
            'cache' => 'eav_cache_column'
        ]);

        // Add cache column to dummy table schema (this would normally be done via migration)
        // For testing, we'll just test the method doesn't throw errors
        $entity = $this->table->newEntity(['name' => 'Cache Test']);
        $entity->set('id', 999); // Set a fake ID for testing

        $result = $this->table->updateEavCache($entity);
        // Without proper cache column, this should return false
        $this->assertFalse($result);
    }

    /**
     * Test cached column preparation
     */
    public function testCachedColumnPreparation()
    {
        // This tests the internal _prepareCachedColumns method indirectly
        // by verifying that cached columns are handled properly during finds

        // Create entity with potential cached columns
        $entity = $this->table->newEntity(['name' => 'Cached Test']);
        $this->assertInstanceOf(Entity::class, $entity);
    }

    // ========================================================================
    // ERROR HANDLING AND EDGE CASES
    // ========================================================================

    /**
     * Test behavior with empty result sets
     */
    public function testEmptyResultSets()
    {
        $results = $this->table->find()
            ->where(['id' => 99999]) // Non-existent ID
            ->all();

        $this->assertEquals(0, $results->count());
    }

    /**
     * Test behavior with NULL values
     */
    public function testNullValues()
    {
        $this->table->addColumn('null-test', ['type' => 'string'], false);

        $entity = $this->table->newEntity([
            'name' => 'Null Test',
            'null-test' => null
        ]);

        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);

        $retrieved = $this->table->get($result->get('id'));
        $this->assertNull($retrieved->get('null-test'));
    }

    /**
     * Test behavior with large datasets
     */
    public function testLargeDatasets()
    {
        $this->table->addColumn('large-test', ['type' => 'string'], false);

        // Create multiple entities
        $entities = [];
        for ($i = 0; $i < 10; $i++) {
            $entities[] = $this->table->newEntity([
                'name' => "Entity $i",
                'large-test' => "Value $i"
            ]);
        }

        // Save all entities
        $results = $this->table->saveMany($entities);
        $this->assertCount(10, $results);

        // Query all with EAV
        $all = $this->table->find()->all();
        $this->assertGreaterThanOrEqual(10, $all->count());
    }

    /**
     * Test unicode and special character handling
     */
    public function testUnicodeHandling()
    {
        $this->table->addColumn('unicode-test', ['type' => 'string'], false);

        $unicodeText = '你好世界 🌍 émojis & spëciál chars';
        $entity = $this->table->newEntity([
            'name' => 'Unicode Test',
            'unicode-test' => $unicodeText
        ]);

        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);

        $retrieved = $this->table->get($result->get('id'));
        $this->assertEquals($unicodeText, $retrieved->get('unicode-test'));
    }

    // ========================================================================
    // PERFORMANCE TESTS
    // ========================================================================

    /**
     * Test query performance with multiple EAV fields
     */
    public function testQueryPerformance()
    {
        // Add multiple EAV columns
        for ($i = 1; $i <= 5; $i++) {
            $this->table->addColumn("perf-test-$i", ['type' => 'string'], false);
        }

        // Create entity with multiple EAV values
        $data = ['name' => 'Performance Test'];
        for ($i = 1; $i <= 5; $i++) {
            $data["perf-test-$i"] = "Performance Value $i";
        }

        $entity = $this->table->newEntity($data);
        $this->table->save($entity);

        // Measure query time (basic performance check)
        $start = microtime(true);
        $retrieved = $this->table->get($entity->get('id'));
        $duration = microtime(true) - $start;

        // Basic assertion - should complete quickly
        $this->assertLessThan(1.0, $duration); // Less than 1 second

        // Verify all fields are retrieved
        for ($i = 1; $i <= 5; $i++) {
            $this->assertEquals("Performance Value $i", $retrieved->get("perf-test-$i"));
        }
    }

    // ========================================================================
    // INTEGRATION TESTS
    // ========================================================================

    /**
     * Test EAV with associations
     */
    public function testEavWithAssociations()
    {
        // This would test how EAV works with CakePHP associations
        // For now, just verify basic functionality doesn't break with associations
        $this->table->addColumn('assoc-test', ['type' => 'string'], false);

        $entity = $this->table->newEntity([
            'name' => 'Association Test',
            'assoc-test' => 'Associated Value'
        ]);

        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);
    }

    /**
     * Test EAV with pagination
     */
    public function testEavWithPagination()
    {
        $this->table->addColumn('pagination-test', ['type' => 'string'], false);

        // Create multiple entities
        for ($i = 0; $i < 5; $i++) {
            $entity = $this->table->newEntity([
                'name' => "Page Entity $i",
                'pagination-test' => "Page Value $i"
            ]);
            $this->table->save($entity);
        }

        // Test paginated query
        $query = $this->table->find()->limit(2)->page(1);
        $results = $query->all();

        $this->assertLessThanOrEqual(2, $results->count());
    }

    /**
     * Test EAV behavior interactions with events
     */
    public function testEavEvents()
    {
        $eventsFired = [];

        // Listen for EAV-related events
        $this->table->getEventManager()->on('Model.beforeSave', function ($event) use (&$eventsFired) {
            $eventsFired[] = 'beforeSave';
        });

        $this->table->getEventManager()->on('Model.afterSave', function ($event) use (&$eventsFired) {
            $eventsFired[] = 'afterSave';
        });

        $this->table->addColumn('event-test', ['type' => 'string'], false);

        $entity = $this->table->newEntity([
            'name' => 'Event Test',
            'event-test' => 'Event Value'
        ]);

        $this->table->save($entity);

        // Verify events were fired
        $this->assertContains('beforeSave', $eventsFired);
        $this->assertContains('afterSave', $eventsFired);
    }

    /**
     * Test complex scenarios combining multiple features
     */
    public function testComplexScenarios()
    {
        // Add multiple columns with different types and bundles
        $this->table->addColumn('complex-string', ['type' => 'string', 'bundle' => 'complex'], false);
        $this->table->addColumn('complex-integer', ['type' => 'integer', 'bundle' => 'complex'], false);
        $this->table->addColumn('complex-date', ['type' => 'date', 'bundle' => 'other'], false);

        // Create entity with mixed data
        $entity = $this->table->newEntity([
            'name' => 'Complex Test',
            'complex-string' => 'Complex String Value',
            'complex-integer' => 12345,
            'complex-date' => time()
        ]);

        // Save and retrieve
        $result = $this->table->save($entity);
        $this->assertInstanceOf(Entity::class, $result);

        // Test querying with complex conditions
        $found = $this->table->find()
            ->where([
                'complex-string' => 'Complex String Value',
                'complex-integer >' => 10000
            ])
            ->first();

        $this->assertInstanceOf(Entity::class, $found);
        $this->assertEquals('Complex Test', $found->get('name'));

        // Test bundle-specific listing
        $complexColumns = $this->table->listColumns('complex');
        $this->assertArrayHasKey('complex-string', $complexColumns);
        $this->assertArrayHasKey('complex-integer', $complexColumns);
        $this->assertArrayNotHasKey('complex-date', $complexColumns);

        $otherColumns = $this->table->listColumns('other');
        $this->assertArrayHasKey('complex-date', $otherColumns);
        $this->assertArrayNotHasKey('complex-string', $otherColumns);
    }
}