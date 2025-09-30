# Authentication Migration Feature Flag System - Usage Guide

## Overview

The Authentication Migration Feature Flag System provides a safe, progressive migration path from CakePHP 3 to CakePHP 5 authentication. It uses feature flags to enable/disable components during migration with full rollback capabilities.

## Migration Stages

### 1. `STAGE_DISABLED` (0% complete)
- **Purpose**: All migration features disabled, using legacy CakePHP 3 auth
- **Safety**: Maximum safety, no changes to production auth
- **Use**: Default state, emergency rollback target

### 2. `STAGE_SCHEMA_ONLY` (15% complete)
- **Purpose**: New database schema created but not used
- **Safety**: High safety, no functional changes
- **Use**: Database preparation, schema validation

### 3. `STAGE_READ_HYBRID` (35% complete)
- **Purpose**: Read from new schema, write to legacy
- **Safety**: Medium safety, legacy fallback enabled
- **Use**: Data validation, parallel testing

### 4. `STAGE_WRITE_HYBRID` (60% complete)
- **Purpose**: Write to both schemas, read from new
- **Safety**: Medium risk, requires monitoring
- **Use**: Gradual migration with data sync

### 5. `STAGE_FULL_MIGRATION` (85% complete)
- **Purpose**: Full CakePHP 5 auth, legacy fallback disabled
- **Safety**: Higher risk, rollback limited
- **Use**: Near-complete migration

### 6. `STAGE_LEGACY_CLEANUP` (100% complete)
- **Purpose**: Remove legacy auth components
- **Safety**: No rollback available
- **Use**: Final cleanup phase

## Command Line Usage

### Check Migration Status
```bash
bin/cake auth_migration status
```

### Set Migration Stage
```bash
# Move to schema preparation
bin/cake auth_migration stage schema_only

# Move to read hybrid mode
bin/cake auth_migration stage read_hybrid

# Move to full migration
bin/cake auth_migration stage full_migration
```

### Manage Individual Flags
```bash
# Enable new users table
bin/cake auth_migration flag use_new_users_table true

# Disable legacy fallback
bin/cake auth_migration flag enable_legacy_fallback false

# Enable logging
bin/cake auth_migration flag log_auth_operations true
```

### Emergency Rollback
```bash
bin/cake auth_migration rollback --confirm
```

### Check Migration Readiness
```bash
bin/cake auth_migration readiness
```

## Feature Flags Reference

| Flag | Purpose | Risk Level |
|------|---------|------------|
| `use_new_users_table` | Read from CakePHP 5 users table | Medium |
| `use_new_auth_component` | Use CakePHP 5 authentication | High |
| `use_new_password_hasher` | Use CakePHP 5 password hashing | Medium |
| `use_new_token_system` | Use CakePHP 5 token system | Medium |
| `migrate_user_roles` | Migrate role assignments | High |
| `enable_legacy_fallback` | Enable fallback to legacy auth | Safety |
| `log_auth_operations` | Log all auth operations | Performance |

## Safety Monitoring

### HTTP Headers
Monitor these headers in responses during migration:
- `X-Auth-Migration-Stage`: Current migration stage
- `X-Auth-Migration-Progress`: Migration progress percentage
- `X-Auth-Rollback-Safe`: Whether rollback is safe

### Emergency Conditions
The system will automatically trigger emergency rollback if:
- More than 50 consecutive authentication failures
- Error rate exceeds 25%
- Response times exceed 5000ms

### Manual Emergency Rollback
Add `?emergency_rollback=true` to any admin URL to trigger immediate rollback.

## Integration Example

### In Controllers
```php
public function login()
{
    $flags = $this->request->getAttribute('authMigrationFlags');

    if ($flags->isEnabled('use_new_auth_component')) {
        // Use CakePHP 5 authentication
        return $this->newAuthLogin();
    } else {
        // Use legacy CakePHP 3 authentication
        return $this->legacyAuthLogin();
    }
}
```

### In Models
```php
public function findUsers($options = [])
{
    $flags = $this->getFeatureFlags();

    if ($flags->isEnabled('use_new_users_table')) {
        return $this->find()->from('users_v5');
    } else {
        return $this->find()->from('users');
    }
}
```

## Migration Workflow

### Phase 1: Preparation (Stage: DISABLED → SCHEMA_ONLY)
1. Backup production database
2. Create new CakePHP 5 database schema
3. Set stage to `schema_only`
4. Validate schema creation

```bash
bin/cake auth_migration readiness
bin/cake auth_migration stage schema_only
```

### Phase 2: Read Testing (Stage: SCHEMA_ONLY → READ_HYBRID)
1. Migrate user data to new schema
2. Enable read hybrid mode
3. Monitor for read errors
4. Validate data consistency

```bash
bin/cake auth_migration stage read_hybrid
# Monitor logs for errors
tail -f logs/auth_migration.log
```

### Phase 3: Write Testing (Stage: READ_HYBRID → WRITE_HYBRID)
1. Enable write to both schemas
2. Monitor authentication operations
3. Validate password hashing
4. Test role assignments

```bash
bin/cake auth_migration stage write_hybrid
bin/cake auth_migration status
```

### Phase 4: Full Migration (Stage: WRITE_HYBRID → FULL_MIGRATION)
1. Disable legacy fallback
2. Use only CakePHP 5 auth
3. Monitor system performance
4. Validate all auth operations

```bash
bin/cake auth_migration stage full_migration
# Point of no easy return - monitor closely
```

### Phase 5: Cleanup (Stage: FULL_MIGRATION → LEGACY_CLEANUP)
1. Remove legacy auth code
2. Drop legacy database tables
3. Clean up configuration
4. Update documentation

```bash
bin/cake auth_migration stage legacy_cleanup
```

## Rollback Procedures

### Safe Rollback (Stages: DISABLED → WRITE_HYBRID)
```bash
bin/cake auth_migration stage disabled
```

### Emergency Rollback (Any Stage)
```bash
bin/cake auth_migration rollback --confirm
```

### Manual Rollback
1. Set stage to `disabled`
2. Clear authentication cache
3. Restart application servers
4. Validate legacy auth functionality

## Monitoring and Alerts

### Key Metrics to Monitor
- Authentication success/failure rates
- Response times for auth operations
- Database query performance
- Session creation/destruction rates
- Password reset request patterns

### Log Analysis
```bash
# Monitor auth operations
tail -f logs/auth_migration.log | grep "Auth operation"

# Check for errors
grep ERROR logs/auth_migration.log

# Monitor stage changes
grep "Migration stage changed" logs/auth_migration.log
```

### Performance Testing
```bash
# Test login performance
ab -n 100 -c 10 http://localhost:8090/admin/login

# Monitor database queries
# Enable query logging during migration
```

## Troubleshooting

### Common Issues

#### Authentication Failures After Stage Change
- Check database connectivity
- Verify user data migration
- Validate password hash compatibility
- Review session configuration

#### Performance Degradation
- Monitor database query count
- Check for N+1 query problems
- Validate database indexes
- Review caching configuration

#### Data Inconsistency
- Compare legacy vs new data
- Check foreign key constraints
- Validate migration scripts
- Review transaction handling

### Emergency Procedures

#### Complete Authentication Failure
1. Execute emergency rollback
2. Check database connectivity
3. Validate legacy auth configuration
4. Review application logs

#### Data Corruption Detected
1. Stop migration immediately
2. Execute emergency rollback
3. Restore from backup if needed
4. Investigate corruption source

## Configuration Reference

See `config/auth_migration.php` for detailed configuration options including:
- Database connections
- Monitoring thresholds
- Security settings
- Testing configurations

## Best Practices

1. **Always test in staging first**
2. **Monitor closely during each stage**
3. **Have rollback procedures ready**
4. **Backup before each stage transition**
5. **Use gradual rollout during business hours**
6. **Validate data integrity at each stage**
7. **Document all changes and observations**
8. **Have emergency contacts ready**