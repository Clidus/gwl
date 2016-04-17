## Database migration between versions

### Upgrade from v0.4.3 to v0.4.4

```SQL
ALTER TABLE `games` ADD `GBLink` VARCHAR(300) DEFAULT NULL AFTER `Error`;
```